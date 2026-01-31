@extends('adminlte::page')

@section('title', 'Preencher Ficha')

@section('content_header')
<h1>Preencher Ficha</h1>
@stop

@section('content')

<div class="card">
    <div class="card-body">

        <div class="alert alert-warning">
            <strong>Atenção:</strong> Para continuar no sistema, é obrigatório preencher sua ficha.
        </div>

        <form action="{{ route('ficha.store') }}" method="POST">
            @csrf

            {{-- Nome --}}
            <div class="form-group">
                <label>Nome Monitorado *</label>

                <input type="text" name="nome_monitorado"
                    class="form-control @error('nome_monitorado') is-invalid @enderror"
                    value="{{ old('nome_monitorado') }}" required>

                @error('nome_monitorado')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Instagram --}}
            <div class="form-group">
                <label>Instagram Monitorado *</label>

                <input type="text" name="instagram_monitorado"
                    class="form-control @error('instagram_monitorado') is-invalid @enderror"
                    value="{{ old('instagram_monitorado') }}" placeholder="@usuario ou link" required>

                @error('instagram_monitorado')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- WhatsApp --}}
            <div class="form-group">
                <label>WhatsApp Monitorado *</label>

                <input type="text" name="whatsapp_monitorado"
                    class="form-control @error('whatsapp_monitorado') is-invalid @enderror"
                    value="{{ old('whatsapp_monitorado') }}" placeholder="DDD + número" required>

                @error('whatsapp_monitorado')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Parentesco --}}
            <div class="form-group">
                <label>Parentesco *</label>

                <input type="text" name="parentesco" class="form-control @error('parentesco') is-invalid @enderror"
                    value="{{ old('parentesco') }}" required>

                @error('parentesco')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            {{-- Observações --}}
            <div class="form-group">
                <label>Observações *</label>

                <textarea name="observacoes" rows="4" class="form-control @error('observacoes') is-invalid @enderror"
                    required>{{ old('observacoes') }}</textarea>

                @error('observacoes')
                    <span class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="alert alert-info">
                <strong>Importante:</strong><br>
                As informações serão analisadas pelo administrador antes da validação.
            </div>

            <button type="submit" class="btn btn-success">
                Salvar Ficha
            </button>

            <a href="{{ route('ficha.show') }}" class="btn btn-secondary">
                Cancelar
            </a>

        </form>

    </div>
</div>

@stop