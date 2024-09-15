@extends('adminlte::page')

@section('title', 'Asociar Usuario con Departamentos')

@section('content_header')
    <h1>Asociar Usuario con Departamentos</h1>
@stop

@section('content')
    <form action="{{ route('usuariosdepartamentos.storeAssociation') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="usuario_id">Seleccionar Usuario</label>
            <select name="usuario_id" id="usuario_id" class="form-control" required>
                <option value="">Seleccione un usuario</option>
                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->nombre }} {{ $usuario->apellido }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="departamentos">Seleccionar Departamentos</label>
            <select name="departamentos[]" id="departamentos" class="form-control" multiple required>
                @foreach ($departamentos as $departamento)
                    <option value="{{ $departamento->id }}">{{ $departamento->nombre }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Asociar Departamentos</button>
    </form>
@stop
