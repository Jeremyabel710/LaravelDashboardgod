@extends('adminlte::page')

@section('title', 'Usuarios')

@section('content_header')
<h1>Lista de Usuarios</h1>
@stop

@section('content')

<!-- Botón para abrir el modal de creación -->
<button class="btn btn-primary mb-3" id="openCreateUserModal">Nuevo Usuario</button>
<form method="GET" action="{{ route('usuarios.index') }}" class="mb-3" id="filterForm">
    <div class="form-row align-items-center">
        <div class="col-auto">
            <label for="departamento" class="col-form-label">Filtrar por etiqueta:</label>
        </div>
        <div class="col-auto">
            <select name="departamento_id" id="departamento" class="form-control">
                <option value="">Todos</option>
                @foreach($departamentos as $departamento)
                <option value="{{ $departamento->id }}"
                    {{ request('departamento_id') == $departamento->id ? 'selected' : '' }}>
                    {{ $departamento->nombre }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="button" id="applyFilter" class="btn btn-primary">Aplicar filtro</button>
        </div>
    </div>
</form>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Etiqueta</th>
            <th>Número</th>
            <th>Email</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody id="usuariosTableBody">
        @forelse($usuarios as $usuario)
        <tr>
            <td>{{ $usuario->id }}</td>
            <td>{{ $usuario->nombre }}</td>
            <td>{{ $usuario->apellido }}</td>
            <td>
                @if($usuario->departamentos->isNotEmpty())
                @foreach($usuario->departamentos as $departamento)
                @php
                $badgeClass = '';
                switch ($departamento->nombre) {
                case 'Administrador':
                $badgeClass = 'badge-danger';
                break;
                case 'Desarrollador':
                $badgeClass = 'badge-primary';
                break;
                case 'Analista':
                $badgeClass = 'badge-success';
                break;
                case 'Soporte':
                $badgeClass = 'badge-warning';
                break;
                case 'Practicante':
                $badgeClass = 'badge-info';
                break;
                default:
                $badgeClass = 'badge-secondary';
                break;
                }
                @endphp
                <span class="badge {{ $badgeClass }}">{{ $departamento->nombre }}</span>
                @endforeach
                @else
                <span class="badge badge-secondary">sin etiquetas</span>
                @endif
            </td>


            <td>{{ $usuario->telefono }}</td>
            <td>{{ $usuario->email }}</td>
            <td>
                <button class="btn btn-warning btn-sm openEditUserModal" data-id="{{ $usuario->id }}">Editar</button>
                <button class="btn btn-danger btn-sm openDeleteUserModal" data-id="{{ $usuario->id }}" data-name="{{ $usuario->nombre }}">Eliminar</button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="7">No se encontraron usuarios</td>
        </tr>
        @endforelse
    </tbody>
</table>
<!-- Enlaces de paginación -->
<div class="d-flex justify-content-center align-items-center">
    @if ($usuarios->onFirstPage())
    <button class="btn btn-secondary" disabled>Anterior</button>
    @else
    <a href="{{ $usuarios->previousPageUrl() }}" class="btn btn-primary">Anterior</a>
    @endif

    <span class="mx-3">Página {{ $usuarios->currentPage() }} de {{ $usuarios->lastPage() }}</span>

    @if ($usuarios->hasMorePages())
    <a href="{{ $usuarios->nextPageUrl() }}" class="btn btn-primary">Siguiente</a>
    @else
    <button class="btn btn-secondary" disabled>Siguiente</button>
    @endif
</div>
<!-- Modal para crear usuario -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="createUserModalContent">
            <!-- Aquí se cargará dinámicamente el contenido del modal -->
        </div>
    </div>
</div>

<!-- Modal para editar usuario -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" id="editUserModalContent">
            <!-- Aquí se cargará dinámicamente el contenido del modal de edición -->
        </div>
    </div>
</div>

<!-- Modal para eliminar usuario -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">Eliminar Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ¿Estás seguro de que deseas eliminar al usuario <span id="deleteUserName"></span>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Pantalla de carga -->
<div id="loading" style="display:none;">
    <div class="spinner-border" role="status">
        <span class="sr-only">Cargando...</span>
    </div>
</div>

@stop

@section('css')
<style>
    #loading {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        display: none;
    }

    .badge {
        font-size: 0.8rem;
        /* Aumenta el tamaño de la fuente */
        padding: 0.5rem 0.65rem;
        /* Ajusta el padding */
    }

    .badge-danger {
        background-color: #dc3545;
        /* Rojo para Administrador */
    }

    .badge-primary {
        background-color: #007bff;
        /* Azul para Desarrollador */
    }

    .badge-success {
        background-color: #28a745;
        /* Verde para Analista */
    }

    .badge-warning {
        background-color: #ffc107;
        /* Naranja para Soporte */
    }

    .badge-info {
        background-color: #17a2b8;
        /* Morado para Practicante */
    }

    .badge-secondary {
        background-color: #6c757d;
        /* Gris para sin etiquetas */
    }

    .pagination .page-link {
        font-size: 0.9rem;
        /* Ajustar el tamaño de la fuente */
        padding: 0.25rem 0.5rem;
        /* Ajustar el padding */
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        /* Color para los elementos deshabilitados */
    }
</style>
@stop

@section('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function() {
        // Manejar el clic del botón para abrir el modal de creación
        $('#openCreateUserModal').on('click', function() {
            $('#loading').show();
            $.ajax({
                url: "{{ route('usuarios.create') }}",
                method: 'GET',
                success: function(response) {
                    $('#createUserModalContent').html(response);
                    $('#createUserModal').modal('show');
                },
                error: function() {
                    alert('Error al cargar el formulario de creación.');
                },
                complete: function() {
                    $('#loading').hide();
                }
            });
        });

        // Manejar el clic del botón para abrir el modal de edición
        $(document).on('click', '.openEditUserModal', function() {
            var userId = $(this).data('id');
            $('#loading').show();
            $.ajax({
                url: "{{ url('usuarios') }}/" + userId + "/edit",
                method: 'GET',
                success: function(response) {
                    $('#editUserModalContent').html(response);
                    $('#editUserModal').modal('show');
                },
                error: function() {
                    alert('Error al cargar el formulario de edición.');
                },
                complete: function() {
                    $('#loading').hide();
                }
            });
        });

        // Manejar el clic del botón de eliminación
        $(document).on('click', '.openDeleteUserModal', function() {
            const userId = $(this).data('id');
            const userName = $(this).data('name');
            $('#deleteUserName').text(userName); // Mostrar el nombre del usuario a eliminar
            $('#confirmDeleteButton').data('id', userId); // Asignar el ID al botón de confirmar
            $('#deleteUserModal').modal('show'); // Abrir el modal de eliminación
        });

        // Confirmar eliminación de usuario
        $('#confirmDeleteButton').on('click', function() {
            const userId = $(this).data('id');
            $('#loading').show(); // Mostrar la pantalla de carga

            $.ajax({
                url: `/usuarios/${userId}`, // URL del método destroy
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}' // Agregar el token CSRF
                },
                success: function(response) {
                    alert(response.message); // Mostrar mensaje de éxito
                    $('#deleteUserModal').modal('hide'); // Cerrar el modal 
                    // Eliminar la fila correspondiente en la tabla
                    $('table tbody tr').each(function() {
                        if ($(this).find('td').first().text() == userId) {
                            $(this).remove(); // Eliminar la fila del DOM
                        }
                    });
                },
                error: function(xhr) {
                    // Manejar el error
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        alert(xhr.responseJSON.message); // Mostrar mensaje de error desde la respuesta
                    } else {
                        alert('Error al eliminar el usuario.'); // Mensaje de error genérico
                    }
                },
                complete: function() {
                    $('#loading').hide(); // Ocultar la pantalla de carga
                }
            });
        });

        // Manejar el clic del botón para aplicar el filtro
        $('#applyFilter').on('click', function() {
            var departamentoId = $('#departamento').val();
            $('#loading').show(); // Mostrar la pantalla de carga

            $.ajax({
                url: "{{ route('usuarios.index') }}", // URL del índice de usuarios
                method: 'GET',
                data: {
                    departamento_id: departamentoId
                }, // Enviar el ID del departamento
                success: function(response) {
                    // Actualizar la tabla con la nueva lista de usuarios
                    $('table tbody').html($(response).find('table tbody').html()); // Reemplazar solo el contenido del tbody
                    $('.d-flex').html($(response).find('.d-flex').html()); // Reemplazar la paginación
                },
                error: function() {
                    alert('Error al aplicar el filtro.'); // Mensaje de error
                },
                complete: function() {
                    $('#loading').hide(); // Ocultar la pantalla de carga
                }
            });
        });

        // Manejar clics en enlaces de paginación
        $(document).on('click', '.d-flex a', function(e) {
            e.preventDefault(); // Prevenir el comportamiento por defecto del enlace
            var url = $(this).attr('href'); // Obtener la URL de la página
            $('#loading').show(); // Mostrar la pantalla de carga

            $.ajax({
                url: url, // Hacer la solicitud a la nueva URL
                method: 'GET',
                data: {
                    departamento_id: $('#departamento').val()
                }, // Enviar el ID del departamento actual
                success: function(response) {
                    // Actualizar la tabla con la nueva lista de usuarios
                    $('table tbody').html($(response).find('table tbody').html()); // Reemplazar solo el contenido del tbody
                    $('.d-flex').html($(response).find('.d-flex').html()); // Reemplazar la paginación
                },
                error: function() {
                    alert('Error al cambiar de página.'); // Mensaje de error
                },
                complete: function() {
                    $('#loading').hide(); // Ocultar la pantalla de carga
                }
            });
        });
    });
</script>
@stop