@extends('adminlte::page')

@section('title', 'Usuários')

@section('content_header')
<h1>Usuários</h1>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="card">
    <div class="card-body">

        <form method="GET" action="{{ route('admin.usuarios.index') }}" class="mb-3">
            <div class="row">
                <div class="col-md-5 mb-2">
                    <input type="text" name="q" class="form-control" value="{{ $q ?? '' }}"
                        placeholder="Buscar por nome, email, whatsapp ou cidade">
                </div>

                <div class="col-md-3 mb-2">
                    <select name="status" class="form-control">
                        <option value="">-- Status (todos) --</option>
                        <option value="pendente" {{ ($status ?? '') === 'pendente' ? 'selected' : '' }}>pendente</option>
                        <option value="ativo" {{ ($status ?? '') === 'ativo' ? 'selected' : '' }}>ativo</option>
                        <option value="bloqueado" {{ ($status ?? '') === 'bloqueado' ? 'selected' : '' }}>bloqueado
                        </option>
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <select name="role" class="form-control">
                        <option value="">-- Perfil (todos) --</option>
                        <option value="user" {{ ($role ?? '') === 'user' ? 'selected' : '' }}>user</option>
                        <option value="admin" {{ ($role ?? '') === 'admin' ? 'selected' : '' }}>admin</option>
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <button class="btn btn-primary btn-block" type="submit">
                        Filtrar
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>WhatsApp</th>
                        <th>Cidade</th>
                        <th>Status</th>
                        <th>Role</th>
                        <th width="120">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($usuarios as $u)
                        <tr>
                            <td>{{ $u->id }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td>{{ $u->whatsapp }}</td>
                            <td>{{ $u->cidade }}</td>
                            <td>
                                @if($u->status === 'ativo')
                                    <span class="badge badge-success">ativo</span>
                                @elseif($u->status === 'bloqueado')
                                    <span class="badge badge-danger">bloqueado</span>
                                @else
                                    <span class="badge badge-warning">pendente</span>
                                @endif
                            </td>
                            <td>
                                @if($u->role === 'admin')
                                    <span class="badge badge-info">admin</span>
                                @else
                                    <span class="badge badge-secondary">user</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.usuarios.show', $u->id) }}" class="btn btn-sm btn-outline-primary">
                                    Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">Nenhum usuário encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $usuarios->links() }}
    </div>
</div>

@stop