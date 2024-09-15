@extends('adminlte::page')

@section('title', 'Editar Departamento')

@section('content_header')
    <h1>Editar Departamento</h1>
@stop

@section('content')
<div class="container">
    <form action="{{ route('departamentos.update', $departamento->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $departamento->nombre) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
@stop

@section('js')
    <script> console.log("Edit Department page loaded."); </script>
@stop
