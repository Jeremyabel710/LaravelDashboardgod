@extends('adminlte::page')

@section('title', 'Usuario-Departamento')

@section('content_header')
    <h1>Lista de Usuario-Departamento</h1>
@stop

@section('content')
    <!-- Botón para asociar usuarios con departamentos -->
    <a href="{{ route('usuariosdepartamentos.associate') }}" class="btn btn-primary mb-3">Asociar Usuario</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Departamentos</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usuarios as $usuario)
                <tr>
                    <td>{{ $usuario->nombre }} {{ $usuario->apellido }}</td>
                    <td>{{ $usuario->telefono }}</td>
                    <td>
                        @if($usuario->departamentos->isNotEmpty())
                            {{ $usuario->departamentos->pluck('nombre')->join(', ') }}
                        @else
                            No asignado
                        @endif
                    </td>
                    <td>
                        <form action="{{ route('usuariosdepartamentos.destroy', $usuario->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?');">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No se encontraron usuarios</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@stop

@section('css')
    <!-- Puedes agregar estilos personalizados aquí -->
@stop

@section('js')
    <script> console.log("Vista de lista de Usuario-Departamento cargada"); </script>
@stop