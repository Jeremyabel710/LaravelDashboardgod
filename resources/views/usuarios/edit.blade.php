<div class="modal-header">
    <h5 class="modal-title" id="editUserModalLabel">Editar Usuario</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <form id="editUserForm">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $usuario->nombre) }}" required>
            @error('nombre')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" name="apellido" id="apellido" class="form-control @error('apellido') is-invalid @enderror" value="{{ old('apellido', $usuario->apellido) }}" required>
            @error('apellido')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $usuario->email) }}" required>
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="telefono">Número</label>
            <div class="input-group">
                <select class="form-select" id="prefijo" name="prefijo" required>
                    <option value="51" @if($usuario->prefijo == '51') selected @endif>+51 (Perú)</option>
                    <option value="54" @if($usuario->prefijo == '54') selected @endif>+54 (Argentina)</option>
                    <option value="55" @if($usuario->prefijo == '55') selected @endif>+55 (Brasil)</option>
                    <option value="56" @if($usuario->prefijo == '56') selected @endif>+56 (Chile)</option>
                    <option value="57" @if($usuario->prefijo == '57') selected @endif>+57 (Colombia)</option>
                    <option value="506" @if($usuario->prefijo == '506') selected @endif>+506 (Costa Rica)</option>
                    <option value="52" @if($usuario->prefijo == '52') selected @endif>+52 (México)</option>
                    <option value="503" @if($usuario->prefijo == '503') selected @endif>+503 (El Salvador)</option>
                    <option value="504" @if($usuario->prefijo == '504') selected @endif>+504 (Honduras)</option>
                    <option value="505" @if($usuario->prefijo == '505') selected @endif>+505 (Nicaragua)</option>
                    <option value="598" @if($usuario->prefijo == '598') selected @endif>+598 (Uruguay)</option>
                    <option value="1" @if($usuario->prefijo == '1') selected @endif>+1 (Estados Unidos y Canadá)</option>
                    <option value="593" @if($usuario->prefijo == '593') selected @endif>+593 (Ecuador)</option>
                    <option value="58" @if($usuario->prefijo == '58') selected @endif>+58 (Venezuela)</option>
                </select>
                <input type="text" class="form-control" id="telefono" name="telefono" 
                    value="{{ old('telefono', substr($usuario->telefono, strlen($usuario->prefijo))) }}" required>
            </div>
            @error('telefono')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="departamentos">Etiquetas (Departamentos)</label>
            <div id="departamentos">
                @foreach($departamentos as $departamento)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="departamento{{ $departamento->id }}" name="departamentos[]" value="{{ $departamento->id }}" 
                            @if($usuario->departamentos->contains($departamento->id)) checked @endif>
                        <label class="form-check-label" for="departamento{{ $departamento->id }}">
                            {{ $departamento->nombre }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
    </form>
</div>
<script>
    $('#editUserForm').submit(function(event) {
        event.preventDefault();
        $('#loading').show(); // Mostrar la pantalla de carga

        var formData = $(this).serializeArray();
        var telefono = $('#telefono').val().replace(/^0+/, ''); // Eliminar ceros iniciales
        var prefijo = $('#prefijo').val(); // Obtener nuevo prefijo
        
        // Debugging
        console.log("Prefijo antiguo:", "{{ $usuario->prefijo }}");
        console.log("Teléfono ingresado:", telefono);
        
        // Reemplazar el prefijo antiguo por el nuevo prefijo
        var antiguoPrefijo = "{{ $usuario->prefijo }}"; // Obtener el antiguo prefijo
        var telefonoSinPrefijo = telefono;

        // Si el número tiene el prefijo antiguo, eliminarlo
        if (telefono.startsWith(antiguoPrefijo)) {
            telefonoSinPrefijo = telefono.substring(antiguoPrefijo.length);
        }

        // Verificar si el número ya tiene el nuevo prefijo
        if (!telefonoSinPrefijo.startsWith(prefijo)) {
            // Modificar los datos para enviar el número sin prefijo
            formData.push({ name: 'telefono', value: telefonoSinPrefijo });
            formData.push({ name: 'prefijo', value: prefijo }); // Agregar el nuevo prefijo
        } else {
            // Si ya tiene el prefijo, solo enviar el número sin modificar
            formData.push({ name: 'telefono', value: telefonoSinPrefijo });
        }

        $.ajax({
            url: "{{ route('usuarios.update', $usuario->id) }}", // Cambia esta ruta según sea necesario
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#loading').hide(); // Ocultar la pantalla de carga
                $('#editUserModal').modal('hide'); // Ocultar el modal

                // Actualizar la fila correspondiente en la tabla
                var updatedRow = `
                    <td>${response.user.id}</td>
                    <td>${response.user.nombre}</td>
                    <td>${response.user.apellido}</td>
                    <td>`;

                // Aplicar colores a las etiquetas
                if (response.user.departamentos.length > 0) {
                    response.user.departamentos.forEach((departamento) => {
                        let badgeClass = '';
                        switch (departamento.nombre) {
                            case 'Administrador':
                                badgeClass = 'badge-danger';
                                break;
                            case 'Desarrollador':
                                badgeClass = 'badge-primary';
                                break;
                            case 'Analista':
                                badgeClass = 'badge-success';
                                break;
                            case 'Soporte':
                                badgeClass = 'badge-warning';
                                break;
                            case 'Practicante':
                                badgeClass = 'badge-info';
                                break;
                            default:
                                badgeClass = 'badge-secondary';
                                break;
                        }
                        updatedRow += `<span class="badge ${badgeClass}">${departamento.nombre}</span> `;
                    });
                } else {
                    updatedRow += `<span class="badge badge-secondary">sin etiquetas</span>`;
                }

                updatedRow += `</td>
                    <td>${prefijo}${telefonoSinPrefijo}</td> <!-- Mostrar el número con el nuevo prefijo -->
                    <td>${response.user.email}</td>
                    <td>
                        <button class="btn btn-warning btn-sm openEditUserModal" data-id="${response.user.id}">Editar</button>
                        <button class="btn btn-danger btn-sm openDeleteUserModal" data-id="${response.user.id}" data-name="${response.user.nombre}">Eliminar</button>
                    </td>`;

                // Encontrar la fila existente y reemplazarla con la fila actualizada
                $('table tbody tr').each(function() {
                    if ($(this).find('td').first().text() == response.user.id) {
                        $(this).html(updatedRow);
                    }
                });
            },
            error: function(response) {
                $('#loading').hide(); // Ocultar la pantalla de carga
                alert('Error al actualizar el usuario'); // Mostrar el mensaje de error
            }
        });
    });
</script>
