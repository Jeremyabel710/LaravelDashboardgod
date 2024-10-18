<div class="modal-body">
    <form id="createUserForm">
        @csrf
        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" required>
        </div>
        <div class="form-group">
            <label for="apellido">Apellido</label>
            <input type="text" class="form-control" id="apellido" name="apellido" required>
        </div>
        <div class="form-group">
            <label for="telefono">Número</label>
            <div class="input-group">
                <select class="form-select" id="prefijo" name="prefijo" required>
                    <option value="51">+51 (Perú)</option>
                    <option value="54">+54 (Argentina)</option>
                    <option value="55">+55 (Brasil)</option>
                    <option value="56">+56 (Chile)</option>
                    <option value="57">+57 (Colombia)</option>
                    <option value="506">+506 (Costa Rica)</option>
                    <option value="52">+52 (México)</option>
                    <option value="503">+503 (El Salvador)</option>
                    <option value="504">+504 (Honduras)</option>
                    <option value="505">+505 (Nicaragua)</option>
                    <option value="598">+598 (Uruguay)</option>
                    <option value="1">+1 (Estados Unidos y Canadá)</option>
                    <option value="57">+57 (Colombia)</option>
                    <option value="593">+593 (Ecuador)</option>
                    <option value="58">+58 (Venezuela)</option>
                </select>
                <input type="text" class="form-control" id="telefono" name="telefono" required>
            </div>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="departamentos">Etiquetas (Departamentos)</label>
            <div id="departamentos">
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
        <button type="submit" class="btn btn-primary">Crear Usuario</button>
    </form>
</div>
<script>
    $('#createUserForm').submit(function(event) {
        event.preventDefault(); // Prevenir el comportamiento por defecto del formulario
        $('#loading').show(); // Mostrar la pantalla de carga

        // Obtener el número de teléfono y eliminar ceros iniciales
        var telefono = $('#telefono').val().replace(/^0+/, ''); // Eliminar ceros iniciales

        // Obtener el prefijo directamente
        var prefijo = $('#prefijo').val(); // Guardar el prefijo, no necesita ser modificado

        // Agregar el número completo a los datos que se enviarán
        var formData = $(this).serializeArray();
        // Aquí usamos 'telefono' para almacenar solo el número
        formData.push({
            name: 'telefono', // Solo enviar el número
            value: telefono // Solo enviar el número, sin prefijo
        });
        formData.push({
            name: 'prefijo', // También enviar el prefijo
            value: prefijo
        });

        $.ajax({
            url: "{{ route('usuarios.store') }}", // Cambia esta ruta según sea necesario
            method: 'POST',
            data: formData,
            success: function(response) {
                $('#loading').hide(); // Ocultar la pantalla de carga
                $('#createUserModal').modal('hide'); // Ocultar el modal

                // Crear la nueva fila de la tabla
                var newRow = `
                <tr>
                    <td>${response.user.id}</td>
                    <td>${response.user.nombre}</td>
                    <td>${response.user.apellido}</td>
                    <td>`;

                // Aplicar colores a las etiquetas
                if (response.user.departamentos.length > 0) {
                    response.user.departamentos.forEach((departamento) => {
                        let badgeClass = '';

                        // Verificar el nombre del departamento y asignar la clase de color
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

                        // Agregar el badge con el color correspondiente
                        newRow += `<span class="badge ${badgeClass} badge-lg">${departamento.nombre}</span> `;
                    });
                } else {
                    newRow += `<span class="badge badge-secondary badge-lg">sin etiquetas</span>`;
                }

                newRow += `</td>
                    <td>${response.user.telefono}</td>
                    <td>${response.user.email}</td>
                    <td>
                        <button class="btn btn-warning btn-sm openEditUserModal" data-id="${response.user.id}">Editar</button>
                        <button class="btn btn-danger btn-sm openDeleteUserModal" data-id="${response.user.id}" data-name="${response.user.nombre}">Eliminar</button>
                    </td>
                </tr>`;

                // Agregar la nueva fila a la tabla
                $('table tbody').append(newRow);
            },
            error: function(response) {
                $('#loading').hide(); // Ocultar la pantalla de carga
                alert('Error al crear el usuario: ' + response.responseJSON.message); // Mostrar el mensaje de error
            }
        });
    });
</script>



