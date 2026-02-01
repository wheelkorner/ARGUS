@extends('adminlte::page')

@section('title', 'PIX')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="mb-0">{{ $titulo }}</h1>
</div>
@stop

@section('content')

{{-- libera seleção só nessa página, porque aqui PRECISA copiar --}}
<style>
    body {
        user-select: text !important;
    }

    textarea,
    input {
        user-select: text !important;
    }
</style>

@if(empty($pixCopiaECola))
    <div class="alert alert-warning">
        <b>PIX não configurado.</b><br>
        Defina <code>PIX_COPIA_E_COLA</code> no seu <code>.env</code>.
    </div>
@endif

<div class="row">

    {{-- QR CODE --}}
    <div class="col-md-4">
        <div class="card card-primary card-outline">
            <div class="card-header border-0">
                <h3 class="card-title">QR Code</h3>
            </div>

            <div class="card-body text-center">
                <p class="text-muted mb-3">{{ $subtitulo }}</p>

                <div id="qrcode" class="d-inline-block p-2 bg-white" style="border-radius: 12px;"></div>

                <small class="text-muted d-block mt-3">
                    Abra o app do banco e escaneie.
                </small>
            </div>
        </div>
    </div>

    {{-- COPIA E COLA --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header border-0">
                <h3 class="card-title">Código PIX (copia e cola)</h3>
            </div>

            <div class="card-body">

                <div class="form-group">
                    <label>Copie este código e cole no app do seu banco:</label>
                    <textarea id="pixCode" class="form-control" rows="6" readonly
                        style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;">{{ $pixCopiaECola }}</textarea>
                </div>

                <div class="d-flex flex-wrap" style="gap:10px;">
                    <button id="btnCopy" class="btn btn-primary">
                        <i class="fas fa-copy"></i> Copiar código
                    </button>

                    <button id="btnSelect" class="btn btn-outline-secondary">
                        <i class="fas fa-i-cursor"></i> Selecionar tudo
                    </button>
                </div>

                <div id="copyMsg" class="text-success mt-3" style="display:none;">
                    <i class="fas fa-check"></i> Código copiado!
                </div>

                <hr>

                <small class="text-muted">
                    Valor e destinatário são definidos pelo seu banco conforme o payload PIX.
                </small>
            </div>
        </div>
    </div>

</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

<script>
    (function () {
        const pix = @json($pixCopiaECola);

        if (pix && pix.length > 0) {
            new QRCode(document.getElementById("qrcode"), {
                text: pix,
                width: 220,
                height: 220,
                correctLevel: QRCode.CorrectLevel.M
            });
        }

        const codeEl = document.getElementById('pixCode');
        const btnCopy = document.getElementById('btnCopy');
        const btnSelect = document.getElementById('btnSelect');
        const msg = document.getElementById('copyMsg');

        function showCopied() {
            msg.style.display = 'block';
            setTimeout(() => msg.style.display = 'none', 2500);
        }

        btnSelect.addEventListener('click', function () {
            codeEl.focus();
            codeEl.select();
        });

        btnCopy.addEventListener('click', async function () {
            try {
                await navigator.clipboard.writeText(codeEl.value);
                showCopied();
            } catch (e) {
                // fallback antigo
                codeEl.focus();
                codeEl.select();
                document.execCommand('copy');
                showCopied();
            }
        });
    })();
</script>
@stop