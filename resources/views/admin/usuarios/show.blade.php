@extends('adminlte::page')

@section('title', 'Detalhes do Usuário')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
</div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Corrija os erros abaixo:</strong>
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">

    <div class="col-md-5">
        <div class="card">
            <div class="card-header">
                <strong>Usuário</strong>
            </div>
            <div class="card-body">
                <p><strong>ID:</strong> {{ $usuario->id }}</p>
                <p><strong>Nome:</strong> {{ $usuario->name }}</p>
                <p><strong>Email:</strong> {{ $usuario->email }}</p>
                <p><strong>WhatsApp:</strong> {{ $usuario->whatsapp }}</p>
                <p><strong>Cidade:</strong> {{ $usuario->cidade }}</p>

                <p><strong>Status:</strong>
                    @if($usuario->status === 'ativo')
                        <span class="badge badge-success">ativo</span>
                    @elseif($usuario->status === 'bloqueado')
                        <span class="badge badge-danger">bloqueado</span>
                    @else
                        <span class="badge badge-warning">pendente</span>
                    @endif
                </p>

                <p><strong>Role:</strong>
                    @if($usuario->role === 'admin')
                        <span class="badge badge-info">admin</span>
                    @else
                        <span class="badge badge-secondary">user</span>
                    @endif
                </p>

                <div class="mt-3 d-flex" style="gap: 8px;">
                    <form method="POST" action="{{ route('admin.usuarios.ativar', $usuario->id) }}">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-success" type="submit">
                            Ativar
                        </button>
                    </form>

                    <form method="POST" action="{{ route('admin.usuarios.bloquear', $usuario->id) }}">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-danger" type="submit">
                            Bloquear
                        </button>
                    </form>

                    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary">
                        Voltar
                    </a>
                </div>

            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">
                <strong>Ficha</strong>
            </div>
            <div class="card-body">

                @if(!$ficha)
                    <div class="alert alert-warning">
                        Este usuário ainda não tem ficha criada.
                    </div>
                @else
                    <p><strong>Nome monitorado:</strong> {{ $ficha->nome_monitorado }}</p>
                    <p><strong>Instagram monitorado:</strong> {{ $ficha->instagram_monitorado }}</p>
                    <p><strong>WhatsApp monitorado:</strong> {{ $ficha->whatsapp_monitorado }}</p>
                    <p><strong>Parentesco:</strong> {{ $ficha->parentesco }}</p>
                    <p><strong>Observações:</strong><br>{!! nl2br(e($ficha->observacoes)) !!}</p>

                    <p>
                        <strong>Processo:</strong>
                        @if($ficha->ativado_em)
                            <span id="processo-timer"
                                  class="badge badge-info"
                                  data-ativado="{{ $ficha->ativado_em->toIso8601String() }}">
                                Sincronizando...
                            </span>
                        @else
                            <span class="badge badge-secondary">Não ativado</span>
                        @endif
                    </p>

                    <p><strong>Ativado em:</strong>
                        {{ $ficha->ativado_em ? $ficha->ativado_em->format('d/m/Y H:i') : '-' }}
                    </p>

                    <p><strong>Limite de ativação:</strong>
                        {{ $ficha->limite_ativacao ? $ficha->limite_ativacao->format('d/m/Y') : '-' }}
                    </p>

                    <hr>
                @endif

                <form method="POST" action="{{ route('admin.usuarios.instrucoes', $usuario->id) }}">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label>Instruções (Admin)</label>
                        <textarea name="instrucoes" class="form-control" rows="6">{{ old('instrucoes', $ficha->instrucoes ?? '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Limite de ativação</label>
                        <input type="date" name="limite_ativacao" class="form-control"
                               value="{{ old('limite_ativacao', isset($ficha) && $ficha->limite_ativacao ? $ficha->limite_ativacao->format('Y-m-d') : '') }}">
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox"
                               name="info_verificada"
                               class="form-check-input"
                               {{ old('info_verificada', $ficha->info_verificada ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">Informações verificadas</label>
                    </div>

                    <div class="form-group form-check">
                        <input type="checkbox"
                               name="documentos_ok"
                               class="form-check-input"
                               {{ old('documentos_ok', $ficha->documentos_ok ?? false) ? 'checked' : '' }}>
                        <label class="form-check-label">Documentos conferidos</label>
                    </div>

                    <button class="btn btn-primary" type="submit">
                        Salvar
                    </button>
                </form>

            </div>
        </div>
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
        el.textContent = 'Data de ativação inválida';
        el.classList.remove('badge-info');
        el.classList.add('badge-danger');
        return;
    }

    // Busca hora do servidor
    let serverNow;
    try {
        const res = await fetch("{{ route('api.server.time') }}", { credentials: 'same-origin' });
        const data = await res.json();
        serverNow = new Date(data.server_time);
    } catch (e) {
        el.textContent = 'Erro ao sincronizar';
        el.classList.remove('badge-info');
        el.classList.add('badge-danger');
        return;
    }

    // Offset servidor - cliente
    const offset = serverNow.getTime() - Date.now();

    function plural(n, s, p) { return n === 1 ? s : p; }

    function atualizar() {
        const agoraServidor = new Date(Date.now() + offset);

        let diffMs = agoraServidor - ativadoEm;
        if (diffMs < 0) diffMs = 0;

        const totalMinutos = Math.floor(diffMs / 1000 / 60);

        const dias = Math.floor(totalMinutos / (60 * 24));
        const restoDia = totalMinutos % (60 * 24);

        const horas = Math.floor(restoDia / 60);
        const minutos = restoDia % 60;

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
