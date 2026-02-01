/* =========================================
   APP GUARD - watermark só quando tentar imprimir/capturar
   - Print (Ctrl+P / menu / window.print) => watermark aparece
   - PrintScreen/Screenshot => não é confiável em web (depende do SO/browser)
   ========================================= */

(function () {
    let userInfo = null;
    let watermarkTimeout = null;

    async function fetchUserInfo() {
        try {
            const res = await fetch('/api/me', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return null;
            return await res.json();
        } catch (_) {
            return null;
        }
    }

    function ensureWatermarkElement() {
        let wm = document.getElementById('app-watermark');
        if (!wm) {
            wm = document.createElement('div');
            wm.id = 'app-watermark';
            // começa sem texto, invisível (o CSS já deixa opacity 0)
            document.body.appendChild(wm);
        }
        return wm;
    }

    async function showWatermark(seconds = 6) {
        // garante elemento
        const wm = ensureWatermarkElement();

        // garante dados do usuário (sem depender de timing do DOMContentLoaded)
        if (!userInfo || !userInfo.email) {
            userInfo = await fetchUserInfo();
        }

        // se ainda não conseguiu (ex: não logado), usa marca genérica
        const email = userInfo?.email || 'ACESSO RESTRITO';
        const ip = userInfo?.ip ? ` • ${userInfo.ip}` : '';
        wm.textContent = `${email}${ip}`;

        wm.style.opacity = '1';

        clearTimeout(watermarkTimeout);
        watermarkTimeout = setTimeout(() => {
            wm.style.opacity = '0';
        }, seconds * 1000);
    }

    function block(e, msg, mark = true) {
        try {
            e.preventDefault();
            e.stopPropagation();
        } catch (_) { }

        if (mark) showWatermark();
        if (msg) alert(msg);

        return false;
    }

    // =========================
    // PRINT: pega por TODOS os caminhos
    // =========================

    // Ctrl+P / Cmd+P
    document.addEventListener('keydown', function (e) {
        const key = (e.key || '').toLowerCase();
        const ctrlOrCmd = e.ctrlKey || e.metaKey;

        if (ctrlOrCmd && key === 'p') {
            // antes de bloquear, marca
            showWatermark();
            return block(e, 'Impressão bloqueada.');
        }

        // (opcional) bloquear cópia/salvar
        if (ctrlOrCmd && key === 'c') return block(e, 'Cópia bloqueada.');
        if (ctrlOrCmd && key === 's') return block(e, 'Ação bloqueada.');
        if (ctrlOrCmd && key === 'u') return block(e, 'Ação bloqueada.');

        if (key === 'f12') return block(e, 'Ação bloqueada.', false);
    });

    // Menu do navegador -> Imprimir (muitos browsers disparam esses eventos)
    window.addEventListener('beforeprint', function () {
        // Mostra e deixa um pouco mais tempo
        showWatermark(10);
    });

    window.addEventListener('afterprint', function () {
        // some rápido depois que sair da impressão
        const wm = document.getElementById('app-watermark');
        if (wm) wm.style.opacity = '0';
    });

    // Bloqueia window.print() direto
    window.print = function () {
        showWatermark();
        alert('Impressão bloqueada.');
        return;
    };

    // =========================
    // Screenshot/PrintScreen (não confiável)
    // =========================
    document.addEventListener('keydown', function (e) {
        if (e.key === 'PrintScreen') {
            // tenta ao menos marcar se o evento existir no browser
            showWatermark();
            return block(e, 'Captura não permitida.');
        }
    });

    document.addEventListener('keyup', function (e) {
        if (e.key === 'PrintScreen') {
            showWatermark();
            alert('Captura não permitida.');
        }
    });

    // =========================
    // UX / proteção visual
    // =========================
    document.addEventListener('contextmenu', function (e) {
        return block(e, 'Ação bloqueada.', false);
    });

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            document.body.classList.add('screen-blur');
        } else {
            document.body.classList.remove('screen-blur');
        }
    });

    // Pré-carrega userInfo (não depende disso pra funcionar)
    document.addEventListener('DOMContentLoaded', async function () {
        ensureWatermarkElement();
        userInfo = await fetchUserInfo();
    });

})();
