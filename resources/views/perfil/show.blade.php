@extends('adminlte::page')

@section('title', 'Meu Perfil')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
    <h1 class="mb-0">Perfil</h1>

    <ol class="breadcrumb float-sm-right mb-0">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
        <li class="breadcrumb-item active">User Profile</li>
    </ol>
</div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">

    {{-- COLUNA ESQUERDA --}}
    <div class="col-md-3">

        {{-- Profile Image --}}
        <div class="card card-primary card-outline">
            <div class="card-body box-profile">
                <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle" src="{{ Auth::user()->adminlte_image() }}"
                        alt="User profile picture">
                </div>

                <h3 class="profile-username text-center">{{ $user->name }}</h3>

                <p class="text-muted text-center">
                    {{ $user->role ?? 'user' }}
                </p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Status</b>
                        <span class="float-right">
                            @if(($user->status ?? null) === 'ativo')
                                <span class="badge badge-success">ativo</span>
                            @elseif(($user->status ?? null) === 'bloqueado')
                                <span class="badge badge-danger">bloqueado</span>
                            @else
                                <span class="badge badge-warning">pendente</span>
                            @endif
                        </span>
                    </li>

                    <li class="list-group-item">
                        <b>Criado em</b>
                        <span class="float-right">
                            {{ optional($user->created_at)->format('d/m/Y H:i') }}
                        </span>
                    </li>
                </ul>

            </div>
        </div>

        {{-- About Me Box --}}
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">About Me</h3>
            </div>

            <div class="card-body">

                <strong><i class="fas fa-envelope mr-1"></i> E-mail</strong>
                <p class="text-muted mb-0">{{ $user->email }}</p>

                <hr>

                <strong><i class="fas fa-phone mr-1"></i> WhatsApp</strong>
                <p class="text-muted mb-0">{{ $user->whatsapp ?? '—' }}</p>

                <hr>

                <strong><i class="fas fa-map-marker-alt mr-1"></i> Cidade</strong>
                <p class="text-muted mb-0">{{ $user->cidade ?? '—' }}</p>

                <hr>

                <strong><i class="fas fa-user-shield mr-1"></i> Perfil</strong>
                <p class="text-muted mb-0">{{ $user->role ?? 'user' }}</p>

            </div>
        </div>

    </div>

    {{-- COLUNA DIREITA --}}
    <div class="col-md-9">

        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="#dados" data-toggle="tab">Dados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#senha" data-toggle="tab">Trocar senha</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#info" data-toggle="tab">Todas as informações</a>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">

                    {{-- ABA: DADOS --}}
                    <div class="active tab-pane" id="dados">
                        <form class="form-horizontal" method="POST" action="{{ route('perfil.update') }}">
                            @csrf
                            @method('PATCH')

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Nome</label>
                                <div class="col-sm-10">
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $user->name) }}" required>
                                </div>
                            </div>

                            {{-- Email: NÃO editável --}}
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">E-mail</label>
                                <div class="col-sm-10">
                                    <input type="email" class="form-control" value="{{ $user->email }}" readonly
                                        style="background:#f4f6f9; cursor:not-allowed;">
                                    <small class="text-muted">Para alterar o e-mail, fale com o suporte.</small>
                                </div>
                            </div>

                            {{-- WhatsApp: NÃO editável --}}
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">WhatsApp</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" value="{{ $user->whatsapp }}" readonly
                                        style="background:#f4f6f9; cursor:not-allowed;">
                                    <small class="text-muted">Para alterar o WhatsApp, fale com o suporte.</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Cidade</label>
                                <div class="col-sm-10">
                                    <input type="text" name="cidade" class="form-control"
                                        value="{{ old('cidade', $user->cidade) }}">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Salvar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- ABA: SENHA --}}
                    <div class="tab-pane" id="senha">
                        <form class="form-horizontal" method="POST" action="{{ route('perfil.password') }}">
                            @csrf
                            @method('PATCH')

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Senha atual</label>
                                <div class="col-sm-9">
                                    <input type="password" name="senha_atual" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Nova senha</label>
                                <div class="col-sm-9">
                                    <input type="password" name="nova_senha" class="form-control" required>
                                    <small class="text-muted">Mínimo 8 caracteres.</small>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label">Confirmar</label>
                                <div class="col-sm-9">
                                    <input type="password" name="nova_senha_confirmation" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="offset-sm-3 col-sm-9">
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-key"></i> Alterar senha
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- ABA: TODAS AS INFORMAÇÕES --}}
                    <div class="tab-pane" id="info">
                        <div class="table-responsive">
                            <table class="table table-striped table-valign-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 260px;">Campo</th>
                                        <th>Valor</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attributes as $key => $value)
                                        @php
                                            if ($value instanceof \Carbon\Carbon)
                                                $value = $value->toDateTimeString();
                                            if (is_bool($value))
                                                $value = $value ? 'true' : 'false';
                                            if ($value === null || $value === '')
                                                $value = '—';
                                        @endphp

                                        <tr>
                                            <td><code>{{ $key }}</code></td>
                                            <td class="text-muted">{{ e((string) $value) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <small class="text-muted d-block mt-2">
                            Campos sensíveis (senha/token) não são exibidos.
                        </small>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>
@stop