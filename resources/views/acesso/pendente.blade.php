@extends('adminlte::page')

@section('title', 'Acesso pendente')

@section('content_header')
<h1>Acesso pendente</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <p>Seu cadastro foi realizado, mas seu acesso ainda <strong>não foi ativado</strong>.</p>
        <p>Assim que o administrador liberar, você conseguirá entrar normalmente.</p>

        <a href="{{ route('login') }}" class="btn btn-primary">
            Voltar para o login
        </a>
    </div>
</div>
@stop