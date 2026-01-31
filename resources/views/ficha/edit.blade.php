@extends('adminlte::page')

@section('title', 'Editar Ficha')

@section('content_header')
<h1>Editar Ficha</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        <form action="{{ route('ficha.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Nome Monitorado *</label>
                <input type="text" name="nome_monitorado"
                    class="form-control @error('nome_monitorado') is-invalid @enderror"
                    value="{{ old('nome_monitorado', $ficha->nome_monitorado) }}" required>

                @error('nome_monitorado')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="form-group">
                <label>Instagram Monitorado *</label>
                <input type="text" name="instagram_monitorado"
                    class="form-control @error('instagram_monitorado') is-invalid @enderror"
                    value="{{ old('instagram_monitorado', $ficha->instagram_monitorado) }}" required>

                @error('instagram_monitorado')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="form-group">
                <label>WhatsApp Monitorado *</label>
                <input type="text" name="whatsapp_monitorado"
                    class="form-control @error('whatsapp_monitorado') is-invalid @enderror"
                    value="{{ old('whatsapp_monitorado', $ficha->whatsapp_monitorado) }}" required>

                @error('whatsapp_monitorado')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="form-group">
                <label>Parentesco *</label>
                <input type="text" name="parentesco" class="form-control @error('parentesco') is-invalid @enderror"
                    value="{{ old('parentesco', $ficha->parentesco) }}" required>

                @error('parentesco')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="form-group">
                <label>Observações *</label>
                <textarea name="observacoes" class="form-control @error('observacoes') is-invalid @enderror" rows="4"
                    required>{{ old('observacoes', $ficha->observacoes) }}</textarea>

                @error('observacoes')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                @enderror
            </div>

            <div class="alert alert-info">
                <strong>Observação:</strong> “Informações verificadas” e “Documentos conferidos” são marcados pelo
                Admin.
            </div>

            <button type="submit" class="btn btn-success">
                Salvar
            </button>

            <a href="{{ route('ficha.show') }}" class="btn btn-secondary">
                Cancelar
            </a>
        </form>

    </div>
</div>

@stop