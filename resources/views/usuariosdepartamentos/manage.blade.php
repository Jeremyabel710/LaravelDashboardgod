@extends('adminlte::page')

@section('title', 'Gestionar Departamentos del Usuario')

@section('content_header')
    <h1>Gestionar Departamentos del Usuario</h1>
@stop

@section('content')
    <form action="{{ route('usuariosdepartamentos.storeAssociation') }}" method="POST">
        @csrf
        <input type="hidden" name="usuario_id" value="{{ $usuario->id }}">

        <div class="form-group">
            <label for="departamentos">Seleccionar Departamentos</label>
            <select name="departamentos[]" id="departamentos" class="form-control" multiple required>
                @foreach ($departamentos as $departamento)
                    <option value="{{ $departamento->id }}" 
                        {{ $usuario->departamentos->contains($departamento->id) ? 'selected' : '' }}>
                        {{ $departamento->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Departamentos</button>
    </form>
@stop