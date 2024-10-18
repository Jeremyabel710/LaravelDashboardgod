@extends('adminlte::page')

@section('title', 'Crear Departamento')

@section('content_header')
    <h1>Nueva Etiqueta</h1>
@stop

@section('content')
    <form action="{{ route('departamentos.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="nombre">Nombre del Departamento</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
    </form>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop
    