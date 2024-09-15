@extends('adminlte::page')

@section('title', 'Crear Alerta')

@section('content_header')
    <h1>Crear Alerta</h1>
@stop

@section('content')
    <form action="{{ route('alertas.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="mensaje">Mensaje</label>
            <input type="text" name="mensaje" class="form-control" id="mensaje" value="{{ old('mensaje') }}">
        </div>

        <div class="form-group">
            <label for="tipo_alerta">Tipo de Alerta</label>
            <select name="tipo_alerta" id="tipo_alerta" class="form-control">
                <option value="" disabled selected>Seleccione un tipo</option>
                <option value="departamento">Departamento</option>
                <option value="usuario">Usuario</option>
            </select>
        </div>

        <div id="departamentos_field" class="form-group d-none">
            <label for="departamentos">Departamentos</label>
            <select name="departamentos[]" id="departamentos" class="form-control" multiple>
                @foreach($departamentos as $departamento)
                    <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div id="usuarios_field" class="form-group d-none">
            <label for="usuarios">Usuarios</label>
            <select name="usuarios[]" id="usuarios" class="form-control" multiple>
                @foreach($usuarios as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->nombre }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Crear Alerta</button>
    </form>

    @push('js')
    <script>
        document.getElementById('tipo_alerta').addEventListener('change', function() {
            const tipo = this.value;
            if (tipo === 'departamento') {
                document.getElementById('departamentos_field').classList.remove('d-none');
                document.getElementById('usuarios_field').classList.add('d-none');
            } else if (tipo === 'usuario') {
                document.getElementById('usuarios_field').classList.remove('d-none');
                document.getElementById('departamentos_field').classList.add('d-none');
            } else {
                document.getElementById('departamentos_field').classList.add('d-none');
                document.getElementById('usuarios_field').classList.add('d-none');
            }
        });
    </script>
    @endpush
@stop
