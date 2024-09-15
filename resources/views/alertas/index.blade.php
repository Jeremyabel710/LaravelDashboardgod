@extends('adminlte::page')

@section('title', 'Alertas')

@section('content_header')
    <h1>Alertas</h1>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-3">
        <a href="{{ route('alertas.create') }}" class="btn btn-success">Crear Nueva Alerta</a>
    </div>

    <!-- Alertas y Departamentos -->
    <h3><em>Alertas y Departamentos</em></h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Alerta ID</th>
                <th scope="col">Mensaje</th>
                <th scope="col">Departamentos Alertados</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($alertas as $alerta)
                @if ($alerta->departamentos->isNotEmpty())
                    <tr>
                        <td>{{ $alerta->id }}</td>
                        <td>{{ $alerta->mensaje }}</td>
                        <td>
                            {{ $alerta->departamentos->pluck('nombre')->implode(', ') }}
                        </td>
                        <td>
                            <form action="{{ route('alertas.enviar', $alerta->id) }}" method="post" style="display:inline;">
                                @csrf
                                <input type="hidden" name="enviar_departamento" value="1">
                                <button type="submit" class="btn btn-primary">Enviar Mensaje a Departamentos</button>
                            </form>
                            <form action="{{ route('alertas.destroy', $alerta->id) }}" method="post" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta alerta?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <!-- Alertas y Usuarios -->
    <h3><em>Alertas y Usuarios</em></h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">Alerta ID</th>
                <th scope="col">Mensaje</th>
                <th scope="col">Usuarios Alertados</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($alertas as $alerta)
                @if ($alerta->usuarios->isNotEmpty())
                    <tr>
                        <td>{{ $alerta->id }}</td>
                        <td>{{ $alerta->mensaje }}</td>
                        <td>
                            {{ $alerta->usuarios->pluck('nombre')->implode(', ') }}
                        </td>
                        <td>
                            <form action="{{ route('alertas.enviar', $alerta->id) }}" method="post" style="display:inline;">
                                @csrf
                                <input type="hidden" name="enviar_usuario" value="1">
                                <button type="submit" class="btn btn-primary">Enviar Mensaje a Usuarios</button>
                            </form>
                            <form action="{{ route('alertas.destroy', $alerta->id) }}" method="post" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta alerta?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
@stop
