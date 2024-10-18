<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Departamento;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AlertasController extends Controller
{
    // Muestra la lista de alertas con sus asociaciones
    public function index()
    {
        $alertasDepartamento = Alerta::with('departamentos')->whereHas('departamentos')->paginate(5);
        $alertasUsuario = Alerta::with('usuarios')->whereHas('usuarios')->paginate(5);

        return view('alertas.index', compact('alertasDepartamento', 'alertasUsuario'));
    }

    // Muestra el formulario para crear una nueva alerta
    public function create()
    {
        $departamentos = Departamento::all();
        $usuarios = Usuario::all();

        return view('alertas.create', compact('departamentos', 'usuarios'));
    }

    // Almacena una nueva alerta
    public function store(Request $request)
    {
        // Validar los datos de entrada
        Log::info('Datos recibidos:', $request->all());

        $request->validate([
            'nombre' => 'required|string|max:255',
            'mensaje' => 'required|string|max:255',
            'fecha_envio_programada' => 'nullable|date',
            'tipo_alerta' => 'required|string',
            'departamentos' => 'nullable|array',
            'departamentos.*' => 'exists:departamento,id',
            'usuarios' => 'nullable|array',
            'usuarios.*' => 'exists:usuario,id',
            'archivo' => 'nullable|file|max:2048', // Aceptar otros tipos de archivos, sin limitar a PDF
        ]);

        // Manejo del archivo
        $archivoName = null;
        if ($request->hasFile('archivo')) {
            Log::info('Archivo recibido', ['archivo' => $request->file('archivo')]);

            try {
                // Obtener el nombre original del archivo
                $originalName = $request->file('archivo')->getClientOriginalName();

                // Generar un nombre único
                $uniqueName = time() . '_' . $originalName;

                // Guardar el archivo con el nombre único en la carpeta 'archivos'
                $request->file('archivo')->storeAs('archivos', $uniqueName, 'public');

                // Guardar solo el nombre del archivo (sin la ruta 'archivos/')
                $archivoName = $uniqueName;

                Log::info('Archivo almacenado en', ['path' => $uniqueName]);
            } catch (\Exception $e) {
                Log::error('Error al subir el archivo: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Hubo un problema al subir el archivo.')->withInput();
            }
        } else {
            Log::info('No se recibió archivo');
        }

        // Crear la nueva alerta
        try {
            $alerta = Alerta::create([
                'nombre' => $request->input('nombre'),
                'mensaje' => $request->input('mensaje'),
                'fecha_creacion' => now(),
                'fecha_envio_programada' => $request->input('fecha_envio_programada'),
                'condicion' => 'pendiente',
                'archivo' => $archivoName, // Guardar solo el nombre del archivo en la base de datos
            ]);
            Log::info('Alerta creada con ID: ' . $alerta->id);
        } catch (\Exception $e) {
            Log::error('Error al crear la alerta: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un problema al crear la alerta.')->withInput();
        }

        // Asociar departamentos o usuarios
        $this->asociarDepartamentosOUsuarios($request, $alerta);

        return redirect()->route('alertas.index')->with('success', 'Alerta creada exitosamente.');
    }

    // Método para asociar departamentos o usuarios
    private function asociarDepartamentosOUsuarios(Request $request, Alerta $alerta)
    {
        if ($request->input('tipo_alerta') === 'departamentos') {
            $departamentos = $request->input('departamentos', []);
            if (!empty($departamentos)) {
                $alerta->departamentos()->sync($departamentos);
            }
            $alerta->usuarios()->detach();
        } elseif ($request->input('tipo_alerta') === 'usuarios') {
            $usuarios = $request->input('usuarios', []);
            if (!empty($usuarios)) {
                $alerta->usuarios()->sync($usuarios);
            }
            $alerta->departamentos()->detach();
        }
    }

    // Muestra los detalles de una alerta específica
    public function show(Alerta $alerta)
    {
        return view('alertas.show', compact('alerta'));
    }

    // Muestra el formulario para editar una alerta existente
    public function edit(Alerta $alerta)
    {
        $departamentos = Departamento::all();
        $usuarios = Usuario::all();
        return view('alertas.edit', compact('alerta', 'departamentos', 'usuarios'));
    }

    // Actualiza una alerta existente
    public function update(Request $request, Alerta $alerta)
    {
        $request->validate([
            'mensaje' => 'required|string|max:255',
            'fecha_envio_programada' => 'nullable|date',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $archivoPath = $alerta->archivo; // Mantener la ruta actual del archivo
        if ($request->hasFile('archivo')) {
            if ($archivoPath) {
                Storage::delete($archivoPath); // Eliminar archivo anterior
            }

            try {
                $archivoPath = $request->file('archivo')->store('archivos', 'public'); // Guardar nuevo archivo
            } catch (\Exception $e) {
                Log::error('Error al subir el nuevo archivo: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Hubo un problema al subir el nuevo archivo.')->withInput();
            }
        }

        // Actualizar la alerta
        $alerta->update(array_merge($request->only('mensaje', 'fecha_envio_programada', 'condicion'), ['archivo' => $archivoPath]));

        // Asociar departamentos o usuarios según los datos enviados
        if ($request->has('departamentos')) {
            $alerta->departamentos()->sync($request->input('departamentos'));
        } else {
            $alerta->departamentos()->detach();
        }

        if ($request->has('usuarios')) {
            $alerta->usuarios()->sync($request->input('usuarios'));
        } else {
            $alerta->usuarios()->detach();
        }

        return redirect()->route('alertas.index')->with('success', 'Alerta actualizada correctamente.');
    }

    // Elimina una alerta
    public function destroy($id)
    {
        $alerta = Alerta::findOrFail($id);

        // Eliminar el archivo si existe
        if ($alerta->archivo) {
            Storage::delete($alerta->archivo);
        }

        // Eliminar la alerta y las asociaciones con departamentos y usuarios
        $alerta->departamentos()->detach();
        $alerta->usuarios()->detach();
        $alerta->delete();

        return redirect()->route('alertas.index')->with('success', 'Alerta eliminada correctamente.');
    }

    // Envía mensajes según el tipo de alerta

    public function enviar(Request $request, $id)
    {
        $alerta = Alerta::findOrFail($id);
        $mensaje = $alerta->mensaje;

        $telefonos = []; // Arreglo para almacenar números de teléfono únicos
        $enviosExitosos = true; // Bandera para verificar si hubo algún fallo en el envío

        // Verificar si se envía a los departamentos
        if ($request->has('enviar_departamento')) {
            foreach ($alerta->departamentos as $departamento) {
                foreach ($departamento->usuarios as $usuario) {
                    if (!empty($usuario->telefono)) {
                        $telefonos[$usuario->telefono] = true; // Usar el número como clave para evitar duplicados
                    }
                }
            }
        }
        // Verificar si se envía a los usuarios directamente
        elseif ($request->has('enviar_usuario')) {
            foreach ($alerta->usuarios as $usuario) {
                if (!empty($usuario->telefono)) {
                    $telefonos[$usuario->telefono] = true; // Usar el número como clave para evitar duplicados
                }
            }
        }

        // Token de autorización para la nueva API
        $token = '5RGiHcGTB456d7fwBk6YcuCmM7psGo'; // Reemplaza con tu token real

        // Comprobar si se envía un archivo
        $fileBase64 = null;
        $filename = null;

        try {
            if ($alerta->archivo) {
                // Obtener la ruta del archivo
                $filePath = storage_path('app/public/archivos/' . $alerta->archivo);

                // Verificar que el archivo exista
                if (file_exists($filePath)) {
                    // Codificar el archivo a base64
                    $fileBase64 = base64_encode(file_get_contents($filePath));
                    $filename = basename($alerta->archivo); // Obtener solo el nombre del archivo
                } else {
                    // Manejo de error: el archivo no existe
                    throw new \Exception('El archivo no se encuentra en la ruta especificada.');
                }
            }
        } catch (\Exception $e) {
            Log::error('Error al procesar el archivo: ' . $e->getMessage());
            return redirect()->route('alertas.index')->with('error', 'No se pudo procesar el archivo. Verifique que exista y vuelva a intentarlo.');
        }

        // Iterar sobre los números de teléfono para enviar mensajes
        foreach (array_keys($telefonos) as $telefono) {
            // Formatear el número con el prefijo + y el código de país
            $toWhatsApp = '+' . $telefono; // Usar el número tal como se ingresó (ya incluye el prefijo)

            // Preparar los datos para el envío
            if ($fileBase64) {
                // Si hay un archivo, usar el endpoint para enviar archivo
                $url = 'https://jeremy.senati.buho.xyz/api/message/send/file'; // Actualiza el endpoint según tu API
                $data = [
                    'number' => $toWhatsApp,
                    'message' => $mensaje,
                    'file' => $fileBase64,
                    'filename' => $filename, // Usar el nombre del archivo
                ];
            } else {
                // Si no hay archivo, usar el endpoint antiguo para enviar mensaje de texto
                $url = 'https://jeremy.senati.buho.xyz/api/message/send-text';
                $data = [
                    'number' => $toWhatsApp,
                    'message' => $mensaje,
                ];
            }

            // Iniciar la sesión curl
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ]);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)); // Convertir datos a JSON

            // Ejecutar la petición y obtener la respuesta
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            // Verificar si el mensaje se envió correctamente
            if ($httpCode != 200) {
                $enviosExitosos = false; // Marcar como fallo en el envío
                break; // Salir del bucle si hay un fallo
            }
        }

        // Redirigir a la vista de alertas con un mensaje de éxito o error  
        if ($enviosExitosos) {
            // Actualizar la fecha de envío y la condición
            $alerta->update([
                'fecha_envio' => now(),
                'condicion' => 'enviada', // Actualizar la condición a 'enviado'
            ]);
            return redirect()->route('alertas.index')->with('success', 'Mensajes enviados exitosamente.');
        } else {
            return redirect()->route('alertas.index')->with('error', 'Algunos mensajes no se enviaron. Verifique los números y vuelva a intentarlo.');
        }
    }
}
