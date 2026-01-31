@extends('adminlte::page')

@section('title', 'Minha Ficha')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
</div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@php
    $fichaInexistente = !$ficha;

    // Considera "não preenchida" se faltar qualquer obrigatório
    $fichaIncompleta = false;
    if ($ficha) {
        $fichaIncompleta =
            empty(trim((string) $ficha->nome_monitorado)) ||
            empty(trim((string) $ficha->instagram_monitorado)) ||
            empty(trim((string) $ficha->whatsapp_monitorado)) ||
            empty(trim((string) $ficha->parentesco)) ||
            empty(trim((string) $ficha->observacoes));
    }

    $precisaPreencher = $fichaInexistente || $fichaIncompleta;
@endphp

<div class="card">
    <div class="card-body">

        @if($precisaPreencher)
            <div class="alert alert-warning">
                <strong>Atenção:</strong> sua ficha ainda não foi preenchida.
                Para continuar, complete as informações obrigatórias.
            </div>

            <a href="{{ route('ficha.create') }}" class="btn btn-primary">
                Preencher Ficha
            </a>

        @else

            @php
                $igRaw = trim((string) ($ficha->instagram_monitorado ?? ''));

                $instagramUrl = '';
                if ($igRaw !== '') {
                    if (str_starts_with($igRaw, '@')) {
                        $username = ltrim($igRaw, '@');
                        $instagramUrl = 'https://www.instagram.com/' . $username . '/';
                    } elseif (!str_contains($igRaw, 'http')) {
                        $instagramUrl = 'https://www.instagram.com/' . $igRaw . '/';
                    } else {
                        $instagramUrl = $igRaw;
                    }
                }

                $isSafeInstagramUrl = $instagramUrl !== '' && str_contains($instagramUrl, 'instagram.com/');
            @endphp

            <p><strong>Nome Monitorado:</strong> {{ $ficha->nome_monitorado }}</p>

            <p><strong>Instagram Monitorado:</strong></p>
            @if($isSafeInstagramUrl)
                <div class="d-flex align-items-center" style="gap: 10px; flex-wrap: wrap;">
                    <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer">
                        {{ $instagramUrl }}
                    </a>

                    <a href="{{ $instagramUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-dark">
                        <i class="fab fa-instagram"></i> Abrir Instagram
                    </a>
                </div>
            @else
                <div class="alert alert-warning mb-2">
                    Link do Instagram inválido. Informe algo como:
                    <code>https://www.instagram.com/usuario/</code> ou <code>@usuario</code>
                </div>
            @endif

            <p class="mt-3"><strong>WhatsApp Monitorado:</strong> {{ $ficha->whatsapp_monitorado }}</p>
            <p><strong>Parentesco:</strong> {{ $ficha->parentesco }}</p>

            <p><strong>Observações:</strong><br>
                {!! nl2br(e($ficha->observacoes)) !!}
            </p>

            <hr>

            <p>
                <strong>Processo:</strong>
                @if($ficha->ativado_em)
                    <span id="processo-timer" class="badge badge-info"
                        data-ativado="{{ $ficha->ativado_em->toIso8601String() }}">
                        Sincronizando...
                    </span>
                @else
                    <span class="badge badge-secondary">Não ativado</span>
                @endif
            </p>

            <hr>

            <p><strong>Status (Admin):</strong></p>

            <p>
                <strong>Informações verificadas:</strong>
                @if($ficha->info_verificada)
                    <span class="badge badge-success">Sim</span>
                @else
                    <span class="badge badge-secondary">Não</span>
                @endif
            </p>

            <p>
                <strong>Documentos conferidos:</strong>
                @if($ficha->documentos_ok)
                    <span class="badge badge-success">Sim</span>
                @else
                    <span class="badge badge-secondary">Não</span>
                @endif
            </p>

            <hr>

            <p><strong>Instruções:</strong></p>
            <div class="p-2 border rounded bg-white">
                {!! nl2br(e($ficha->instrucoes)) !!}
            </div>

            <div class="mt-3">
                <a href="{{ route('ficha.edit') }}" class="btn btn-primary">
                    Editar Ficha
                </a>
            </div>

        @endif

    </div>
</div>

@stop

@section('js')
<script>
    (async function () {

        const el = document.getElementById('processo-timer');
        if (!el) return;

        const ativadoEm = new Date(el.dataset.ativado);

        if (isNaN(ativadoEm.getTime())) {
            el.textContent = 'Data inválida';
            el.classList.remove('badge-info');
            el.classList.add('badge-danger');
            return;
        }

        let serverNow;
        try {
            const res = await fetch("{{ route('api.server.time') }}", { credentials: 'same-origin' });
            const data = await res.json();
            serverNow = new Date(data.server_time);
        } catch (e) {
            el.textContent = 'Erro de sincronização';
            el.classList.remove('badge-info');
            el.classList.add('badge-danger');
            return;
        }

        const offset = serverNow.getTime() - Date.now();

        function plural(n, s, p) { return n === 1 ? s : p; }

        function atualizar() {
            const agoraServidor = new Date(Date.now() + offset);

            let diff = agoraServidor - ativadoEm;
            if (diff < 0) diff = 0;

            const totalMin = Math.floor(diff / 1000 / 60);

            const dias = Math.floor(totalMin / (60 * 24));
            const resto = totalMin % (60 * 24);

            const horas = Math.floor(resto / 60);
            const minutos = resto % 60;

            el.textContent =
                `${dias} ${plural(dias, 'dia', 'dias')}, ` +
                `${horas} ${plural(horas, 'hora', 'horas')}, ` +
                `${minutos} ${plural(minutos, 'minuto', 'minutos')}`;
        }

        atualizar();
        setInterval(atualizar, 1000);

    })();
</script>
@stop