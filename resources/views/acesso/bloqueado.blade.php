@extends('adminlte::page')

@section('title', 'Acesso bloqueado')

@section('content_header')
<h1>Acesso bloqueado</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <p>Seu acesso foi <strong>bloqueado</strong>.</p>
        <p>Se você acredita que isso é um engano, entre em contato com o administrador.</p>

        <a href="{{ route('login') }}" class="btn btn-primary">
            Voltar para o login
        </a>
    </div>
</div>
@stop