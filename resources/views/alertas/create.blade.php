<div class="modal-header">
    <h5 class="modal-title" id="createAlertModalLabel">Crear Nueva Alerta</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <form id="createAlertForm" method="POST" action="{{ route('alertas.store') }}" enctype="multipart/form-data"> <!-- Añadir acción aquí -->
        @csrf
        <div class="form-group">
            <label for="nombre">Nombre de la Alerta</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="mensaje">Mensaje</label>
            <textarea class="form-control" id="mensaje" name="mensaje" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label for="fecha_envio_programada">Fecha de Envío Programada</label>
            <input type="datetime-local" class="form-control" id="fecha_envio_programada" name="fecha_envio_programada" required>
        </div>
        <div class="form-group">
            <label for="tipo_alerta">Tipo de Alerta</label>
            <select class="form-control" id="tipo_alerta" name="tipo_alerta" required>
                <option value="" disabled selected>Seleccione un tipo de alerta</option>
                <option value="usuarios">Usuarios</option>
                <option value="departamentos">Departamentos</option>
            </select>
        </div>
        <div class="form-group" id="usuarios" style="display: none;">
            <label>Seleccionar Usuarios</label>
            <div id="usuariosList">
                @foreach($usuarios as $usuario)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="usuario{{ $usuario->id }}" name="usuarios[]" value="{{ $usuario->id }}">
                    <label class="form-check-label" for="usuario{{ $usuario->id }}">
                        {{ $usuario->nombre }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>
        <div class="form-group" id="departamentos" style="display: none;">
            <label>Seleccionar Departamentos</label>
            <div id="departamentosList">
                @foreach($departamentos as $departamento)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="departamento{{ $departamento->id }}" name="departamentos[]" value="{{ $departamento->id }}">
                    <label class="form-check-label" for="departamento{{ $departamento->id }}">
                        {{ $departamento->nombre }}
                    </label>
                </div>
                @endforeach
            </div>
        </div>
        <div class="form-group">
            <label for="archivo">Seleccionar archivo:</label>
            <input type="file" name="archivo" id="archivo" accept=".pdf, .doc, .docx, .xls, .xlsx, .ppt, .pptx, .jpg, .jpeg, .png, .txt" />
        </div>
        <button type="submit" class="btn btn-primary">Crear Alerta</button>
    </form>
</div>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script>
    $(document).ready(function() {
        // Mostrar/ocultar secciones de usuarios o departamentos basadas en la selección del tipo de alerta
        $('#tipo_alerta').change(function() {
            const selectedType = $(this).val();
            if (selectedType === 'usuarios') {
                $('#usuarios').show(); // Muestra la sección de usuarios
                $('#departamentos').hide(); // Oculta la sección de departamentos
            } else if (selectedType === 'departamentos') {
                $('#departamentos').show(); // Muestra la sección de departamentos
                $('#usuarios').hide(); // Oculta la sección de usuarios
            } else {
                $('#usuarios').hide(); // Oculta la sección de usuarios
                $('#departamentos').hide(); // Oculta la sección de departamentos
            }
        });

        // Manejo del cierre del modal
        $('#createAlertForm').on('submit', function() {
            // Cierra el modal al enviar el formulario
            $('#createAlertModal').modal('hide');
        });
    });
</script>