<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\Departamento;
use App\Models\Usuario;
use Illuminate\Http\Request;

class AlertasController extends Controller
{
    // Muestra la lista de alertas con sus asociaciones
    public function index()
    {
        // Obtener todas las alertas con sus relaciones
        $alertas = Alerta::with('departamentos', 'usuarios')->get();

        // Obtener los datos específicos de las tablas pivot
        $alertasDepartamentos = Alerta::select('id', 'mensaje')
            ->with(['departamentos' => function($query) {
                $query->select('departamento.id', 'departamento.nombre');
            }])->get();

        $alertasUsuarios = Alerta::select('id', 'mensaje')
            ->with(['usuarios' => function($query) {
                $query->select('usuario.id', 'usuario.nombre');
            }])->get();

        return view('alertas.index', compact('alertas', 'alertasDepartamentos', 'alertasUsuarios'));
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
        $request->validate([
            'mensaje' => 'required|string|max:255',
            'tipo_alerta' => 'required|string',
            'departamentos' => 'nullable|array',
            'departamentos.*' => 'exists:departamento,id',
            'usuarios' => 'nullable|array',
            'usuarios.*' => 'exists:usuario,id',
        ]);

        // Crear la nueva alerta
        $alerta = Alerta::create([
            'mensaje' => $request->input('mensaje'),
            'fecha_creacion' => now(),
        ]);

        // Asociar departamentos o usuarios según el tipo de alerta
        if ($request->input('tipo_alerta') === 'departamento') {
            $departamentos = $request->input('departamentos', []);
            if (!empty($departamentos)) {
                $alerta->departamentos()->sync($departamentos);
            }
            // Desasociar usuarios si el tipo de alerta es 'departamento'
            $alerta->usuarios()->detach();
        } elseif ($request->input('tipo_alerta') === 'usuario') {
            $usuarios = $request->input('usuarios', []);
            if (!empty($usuarios)) {
                $alerta->usuarios()->sync($usuarios);
            }
            // Desasociar departamentos si el tipo de alerta es 'usuario'
            $alerta->departamentos()->detach();
        }

        return redirect()->route('alertas.index')->with('success', 'Alerta creada exitosamente.');
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
        // Validar los datos de entrada
        $request->validate([
            'mensaje' => 'required|string|max:255',
            'fecha_creacion' => 'required|date',
        ]);

        // Actualizar la alerta
        $alerta->update($request->only('mensaje', 'fecha_creacion'));

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

        // Configuración de Twilio
        $accountSid = ''; // SID de cuenta Twilio
        $authToken = ''; // Token de autenticación Twilio
        $fromWhatsApp = 'whatsapp:+14155238886'; // Número de Twilio para WhatsApp

        foreach (array_keys($telefonos) as $telefono) {
            $toWhatsApp = 'whatsapp:+51' . $telefono; // Asumir código de país +51

            // URL de la API de Twilio
            $url = 'https://api.twilio.com/2010-04-01/Accounts/' . $accountSid . '/Messages.json';

            // Datos a enviar en el POST
            $data = [
                'To' => $toWhatsApp,
                'From' => $fromWhatsApp,
                'Body' => $mensaje
            ];

            // Iniciar la sesión curl
            $curl = curl_init($url);

            // Configurar las opciones de curl
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($curl, CURLOPT_USERPWD, $accountSid . ':' . $authToken);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            // Ejecutar la petición y obtener la respuesta
            $response = curl_exec($curl);

            // Obtener información del código de respuesta HTTP
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            // Cerrar la sesión curl
            curl_close($curl);

            // Verificar si el mensaje se envió correctamente
            if ($httpCode != 201) {
                $enviosExitosos = false; // Marcar como fallo en el envío
                break; // Salir del bucle si hay un fallo
            }
        }

        // Redirigir a la vista de alertas con un mensaje de éxito o error
        if ($enviosExitosos) {
            return redirect()->route('alertas.index')->with('success', 'Mensajes enviados exitosamente.');
        } else {
            return redirect()->route('alertas.index')->with('error', 'Algunos mensajes no se enviaron. Verifique los números y vuelva a intentarlo.');
        }
    }
}
