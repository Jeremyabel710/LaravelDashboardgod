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

<button class="btn btn-primary mb-3" id="openCreateAlertModal">Nueva Alerta</button>

<!-- Alertas y Departamentos -->
<h3><em>Alertas y Departamentos</em></h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Nombre</th>
            <th scope="col">Mensaje</th>
            <th scope="col">Etiqueta</th>
            <th scope="col">Condición</th>
            <th scope="col">Fecha de Envío Programada</th>
            <th scope="col">Archivo</th> <!-- Nueva columna para el archivo -->
            <th scope="col">Acciones</th>
        </tr>
    </thead>
    <tbody id="alertasDepartamentoTableBody">
        @foreach ($alertasDepartamento as $alerta)
        <tr>
            <td>{{ $alerta->id }}</td>
            <td>{{ $alerta->nombre }}</td>
            <td>{{ $alerta->mensaje }}</td>
            <td>{{ implode(', ', $alerta->departamentos->pluck('nombre')->toArray()) }}</td>
            <td>{{ $alerta->condicion }}</td>
            <td>{{ $alerta->fecha_envio_programada ? \Carbon\Carbon::parse($alerta->fecha_envio_programada)->format('Y-m-d H:i:s') : 'No programada' }}</td>
            <td>
                @if($alerta->archivo) <!-- Verificar si hay un archivo -->
                    <a href="{{ asset('storage/archivos/' . $alerta->archivo) }}" target="_blank">Ver archivo</a>
                @else
                    Sin archivo
                @endif
            </td>
            <td>
                <div class="btn-group" role="group">
                    <form action="{{ route('alertas.enviar', $alerta->id) }}" method="post" style="display:inline;">
                        @csrf
                        <input type="hidden" name="enviar_departamento" value="1">
                        <button type="submit" class="btn btn-primary">Enviar a Departamentos</button>
                    </form>
                    <form action="{{ route('alertas.destroy', $alerta->id) }}" method="post" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta alerta?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>


<!-- Enlaces de paginación para Alertas de Departamentos -->
@if ($alertasDepartamento->count())
<div class="d-flex justify-content-center align-items-center mb-4">
    @if ($alertasDepartamento->onFirstPage())
    <button class="btn btn-secondary" disabled>Anterior</button>
    @else
    <a href="{{ $alertasDepartamento->previousPageUrl() }}" class="btn btn-primary">Anterior</a>
    @endif

    <span class="mx-3">Página {{ $alertasDepartamento->currentPage() }} de {{ $alertasDepartamento->lastPage() }}</span>

    @if ($alertasDepartamento->hasMorePages())
    <a href="{{ $alertasDepartamento->nextPageUrl() }}" class="btn btn-primary">Siguiente</a>
    @else
    <button class="btn btn-secondary" disabled>Siguiente</button>
    @endif
</div>
@endif

<!-- Alertas y Usuarios -->
<h3><em>Alertas y Usuarios</em></h3>
<table class="table table-striped">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Nombre</th>
            <th scope="col">Mensaje</th>
            <th scope="col">Usuarios Alertados</th>
            <th scope="col">Condición</th>
            <th scope="col">Fecha de Envío Programada</th>
            <th scope="col">Acciones</th>
        </tr>
    </thead>
    <tbody id="alertasUsuarioTableBody">
        @foreach ($alertasUsuario as $alerta)
        <tr>
            <td>{{ $alerta->id }}</td>
            <td>{{ $alerta->nombre }}</td>
            <td>{{ $alerta->mensaje }}</td>
            <td>{{ $alerta->usuarios->pluck('nombre')->implode(', ') }}</td>
            <td>{{ $alerta->condicion }}</td>
            <td>{{ $alerta->fecha_envio_programada ? \Carbon\Carbon::parse($alerta->fecha_envio_programada)->format('Y-m-d H:i:s') : 'No programada' }}</td>
            <td>
                @if($alerta->archivo) <!-- Verificar si hay un archivo -->
                    <a href="{{ asset('storage/archivos/' . $alerta->archivo) }}" target="_blank">Ver archivo</a>
                @else
                    Sin archivo
                @endif
            </td>
            <td>
                <div class="btn-group" role="group">
                    <form action="{{ route('alertas.enviar', $alerta->id) }}" method="post" style="display:inline;">
                        @csrf
                        <input type="hidden" name="enviar_usuario" value="1">
                        <button type="submit" class="btn btn-primary">Enviar a Usuarios</button>
                    </form>
                    <form action="{{ route('alertas.destroy', $alerta->id) }}" method="post" style="display:inline;" onsubmit="return confirm('¿Estás seguro de que deseas eliminar esta alerta?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Enlaces de paginación para Alertas de Usuarios -->
@if ($alertasUsuario->count())
<div class="d-flex justify-content-center align-items-center mb-4">
    @if ($alertasUsuario->onFirstPage())
    <button class="btn btn-secondary" disabled>Anterior</button>
    @else
    <a href="{{ $alertasUsuario->previousPageUrl() }}" class="btn btn-primary">Anterior</a>
    @endif

    <span class="mx-3">Página {{ $alertasUsuario->currentPage() }} de {{ $alertasUsuario->lastPage() }}</span>

    @if ($alertasUsuario->hasMorePages())
    <a href="{{ $alertasUsuario->nextPageUrl() }}" class="btn btn-primary">Siguiente</a>
    @else
    <button class="btn btn-secondary" disabled>Siguiente</button>
    @endif
</div>
@endif

<!-- Modal para crear nueva alerta -->
<div class="modal fade" id="createAlertModal" tabindex="-1" aria-labelledby="createAlertModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="createAlertModalContent">
            <!-- Aquí se cargará dinámicamente el contenido del modal -->
        </div>
    </div>
</div>

<div id="loading" style="display:none;">
    <div class="spinner-border" role="status">
        <span class="sr-only">Cargando...</span>
    </div>
</div>
@stop

@section('css')
<style>
    /* Estilos para la pantalla de carga */
    #loading {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
    }

    .badge {
        font-size: 0.8rem;
        /* Aumenta el tamaño de la fuente */
        padding: 0.5rem 0.65rem;
        /* Ajusta el padding */
    }
</style>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Manejar el clic del botón para abrir el modal de creación
        $('#openCreateAlertModal').on('click', function() {
            $('#loading').show();
            $.ajax({
                url: "{{ route('alertas.create') }}",
                method: 'GET',
                success: function(response) {
                    $('#createAlertModalContent').html(response);
                    $('#createAlertModal').modal('show');
                },
                error: function() {
                    alert('Error al cargar el formulario de creación.');
                },
                complete: function() {
                    $('#loading').hide();
                }
            });
        });

        /*// Manejar la respuesta de creación de alerta
        $('body').on('submit', '#createAlertForm', function(e) {
            e.preventDefault();
            $('#loading').show();

            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: $(this).serialize(),
                success: function(response) {
                    // Agregar la nueva alerta a la tabla correspondiente
                    // Actualiza la tabla de alertas de departamentos o usuarios según sea necesario
                    $('#alertasDepartamentoTableBody').append('<tr><td>' + response.alerta.id + '</td><td>' + response.alerta.nombre + '</td><td>' + response.alerta.mensaje + '</td><td>' + response.alerta.etiqueta + '</td><td>' + response.alerta.condicion + '</td><td>' + response.alerta.fecha_envio_programada + '</td><td>...</td></tr>');
                    $('#alertasUsuarioTableBody').append('<tr><td>' + response.alerta.id + '</td><td>' + response.alerta.nombre + '</td><td>' + response.alerta.mensaje + '</td><td>' + response.alerta.usuarios + '</td><td>' + response.alerta.condicion + '</td><td>' + response.alerta.fecha_envio_programada + '</td><td>...</td></tr>');

                    // Cerrar el modal
                    $('#createAlertModal').modal('hide');
                },
                error: function(xhr) {
                    alert('Error al crear la alerta: ' + xhr.responseJSON.message);
                },
                complete: function() {
                    $('#loading').hide();
                }
            });
        });*/
    });
</script>
@stop