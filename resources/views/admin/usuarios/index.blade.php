@extends('adminlte::page')

@section('title', 'Usuários')

@section('content_header')
<div class="d-flex align-items-center justify-content-between">
</div>
@stop

@section('content')

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
    <div class="card-header border-0">
        <h3 class="card-title">Lista de Usuários</h3>

        <div class="card-tools">
            <button type="button" class="btn btn-tool btn-sm" data-card-widget="collapse" title="Recolher">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>

    {{-- TOOLBAR (filtros + busca) --}}
    <div class="card-body pb-0">
        <div class="row align-items-end">
            <div class="col-md-3 mb-2">
                <label class="mb-1">Status</label>
                <select id="filtro-status" class="form-control">
                    <option value="">Todos</option>
                    <option value="ativo">ativo</option>
                    <option value="pendente">pendente</option>
                    <option value="bloqueado">bloqueado</option>
                </select>
            </div>

            <div class="col-md-3 mb-2">
                <label class="mb-1">Role</label>
                <select id="filtro-role" class="form-control">
                    <option value="">Todos</option>
                    <option value="admin">admin</option>
                    <option value="user">user</option>
                </select>
            </div>

            <div class="col-md-6 mb-2">
                <label class="mb-1">Pesquisar</label>
                <div class="input-group">
                    <input id="filtro-busca" type="text" class="form-control"
                        placeholder="Nome, email, whatsapp ou cidade">
                    <div class="input-group-append">
                        <button id="btn-buscar" class="btn btn-primary" type="button" title="Buscar">
                            <i class="fas fa-search"></i>
                        </button>
                        <button id="btn-limpar" class="btn btn-outline-secondary" type="button" title="Limpar filtros">
                            <i class="fas fa-eraser"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <hr class="mt-3 mb-0">
    </div>

    <div class="card-body pt-3">
        <div class="table-responsive">
            <table id="tabela-usuarios" class="table table-bordered table-hover table-striped w-100">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 70px;">#</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th style="width: 160px;">WhatsApp</th>
                        <th style="width: 160px;">Cidade</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 120px;">Role</th>
                        <th class="text-right" style="width: 120px;">Ações</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@stop

@section('css')
{{-- DataTables Bootstrap 4 --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css">

<style>
    /* escondemos o filtro e o length padrão do DataTables (usamos toolbar custom) */
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_length {
        display: none;
    }

    table.dataTable td,
    table.dataTable th {
        vertical-align: middle;
    }

    .table thead th {
        white-space: nowrap;
    }
</style>
@stop

@section('js')
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

<script>
    $(function () {

        const table = $('#tabela-usuarios').DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            pageLength: 10,
            order: [[0, 'desc']],

            // layout sem Buttons
            dom: 'rtip',

            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.8/i18n/pt-BR.json"
            },

            ajax: {
                url: "{{ route('admin.usuarios.datatable') }}",
                data: function (d) {
                    d.status = $('#filtro-status').val() || '';
                    d.role = $('#filtro-role').val() || '';

                    d.search = d.search || {};
                    d.search.value = $('#filtro-busca').val() || '';
                }
            },

            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'whatsapp', name: 'whatsapp' },
                { data: 'cidade', name: 'cidade' },
                { data: 'status_badge', name: 'status', orderable: true, searchable: false },
                { data: 'role_badge', name: 'role', orderable: true, searchable: false },
                { data: 'acoes', orderable: false, searchable: false, className: 'text-right' },
            ],
        });

        // filtros sem reload
        $('#filtro-status, #filtro-role').on('change', function () {
            table.ajax.reload();
        });

        function aplicarBusca() {
            table.ajax.reload();
        }

        $('#btn-buscar').on('click', aplicarBusca);

        $('#filtro-busca').on('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                aplicarBusca();
            }
        });

        $('#btn-limpar').on('click', function () {
            $('#filtro-status').val('');
            $('#filtro-role').val('');
            $('#filtro-busca').val('');
            table.ajax.reload();
        });

    });
</script>
@stop