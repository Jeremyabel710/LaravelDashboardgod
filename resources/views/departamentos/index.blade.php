@extends('adminlte::page')

@section('title', 'Departamentos')

@section('content_header')
    <h1>Lista de Roles</h1>
@stop   

@section('content')
    <a href="{{ route('departamentos.create') }}" class="btn btn-primary mb-3">Nuevo Rol</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Etiqueta</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody> 
            @forelse($departamentos as $departamento)
                <tr>
                    <td>{{ $departamento->id }}</td>
                    <td>{{ $departamento->nombre }}</td>
                    <td>
                        <a href="{{ route('departamentos.edit', $departamento->id) }}" class="btn btn-warning">Editar</a>
                        <form action="{{ route('departamentos.destroy', $departamento->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este departamento?');">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">No se encontraron departamentos</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop
