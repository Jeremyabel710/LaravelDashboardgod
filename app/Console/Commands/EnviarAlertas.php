<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alerta;
use Carbon\Carbon;

class EnviarAlertas extends Command
{
    // El nombre y la descripción del comando
    protected $signature = 'alertas:enviar';
    protected $description = 'Enviar alertas según la fecha programada';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Obtener las alertas pendientes cuya fecha de envío programada ya pasó o es igual al momento actual
        $alertasPendientes = Alerta::with(['departamentos.usuarios', 'usuarios']) // Cargar departamentos, usuarios de departamentos y usuarios asociados
            ->where('condicion', 'pendiente')
            ->where('fecha_envio_programada', '<=', Carbon::now())
            ->get();

        foreach ($alertasPendientes as $alerta) {
            // Aquí puedes reutilizar tu lógica de envío de mensajes
            $this->enviarMensajes($alerta);
        }

        return 0;
    }

    private function enviarMensajes($alerta)
    {
        $mensaje = $alerta->mensaje;
        $telefonos = [];
        $enviosExitosos = true; // Bandera para verificar si hubo algún fallo en el envío

        // Verificar si se envía a los departamentos
        foreach ($alerta->departamentos as $departamento) {
            foreach ($departamento->usuarios as $usuario) {
                if (!empty($usuario->telefono)) {
                    $telefonos[$usuario->telefono] = true; // Usar el número como clave para evitar duplicados
                }
            }
        }

        // Verificar si se envía a los usuarios directamente
        foreach ($alerta->usuarios as $usuario) {
            if (!empty($usuario->telefono)) {
                $telefonos[$usuario->telefono] = true; // Usar el número como clave para evitar duplicados
            }
        }

        // Imprimir los números de teléfono obtenidos
        if (empty($telefonos)) {
            $this->info('No se encontraron números de teléfono para enviar la alerta.');
            return; // Salir del método si no hay números
        }

        $this->info('Números de teléfono obtenidos:');
        foreach (array_keys($telefonos) as $telefono) {
            $this->info($telefono);
        }

        // Token de autorización para la API
        $token = '5RGiHcGTB456d7fwBk6YcuCmM7psGo'; 

        // Comprobar si se envía un archivo
        $fileBase64 = null;
        $filename = null;

        // Verificar si hay un archivo asociado a la alerta
        if ($alerta->archivo) {
            // Obtener la ruta del archivo
            $filePath = storage_path('app/public/archivos/' . $alerta->archivo);

            // Verificar que el archivo exista
            if (file_exists($filePath)) {
                // Codificar el archivo a base64
                $fileBase64 = base64_encode(file_get_contents($filePath));
                $filename = basename($alerta->archivo); // Obtener solo el nombre del archivo
            } else {
                $this->error('El archivo no se encuentra en la ruta especificada.');    
                return; // Salir del método si no se encuentra el archivo
            }
        }

        foreach (array_keys($telefonos) as $telefono) {
            $toWhatsApp = '+' . $telefono; // Usar el número en el formato +51954527028

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

            // Configurar las opciones de curl
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
            ]);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data)); // Convertir datos a JSON

            // Ejecutar la petición y obtener la respuesta
            $response = curl_exec($curl);

            // Obtener información del código de respuesta HTTP
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            // Cerrar la sesión curl
            curl_close($curl);

            // Verificar si el mensaje se envió correctamente
            if ($httpCode != 200) {
                $enviosExitosos = false; // Marcar como fallo en el envío
                break; // Salir del bucle si hay un fallo
            }

            $this->info("Mensaje enviado a {$toWhatsApp}");
        }

        // Actualizar el estado de la alerta después de enviar los mensajes
        if ($enviosExitosos) {
            $alerta->update([
                'fecha_envio' => now(),
                'condicion' => 'enviada', // Actualizar la condición a 'enviado'
            ]);
            $this->info('Todos los mensajes enviados exitosamente.');
        } else {
            $this->error('Algunos mensajes no se enviaron. Verifique los números y vuelva a intentarlo.');
        }
    }
}