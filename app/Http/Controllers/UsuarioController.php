<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use App\Models\Departamento;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        // Obtener el departamento_id del request
        $departamentoId = $request->input('departamento_id');

        // Obtener todos los departamentos para el filtro
        $departamentos = Departamento::all();

        // Obtener usuarios con sus departamentos, filtrando por departamento si se proporciona
        $usuarios = Usuario::with('departamentos')
            ->when($departamentoId, function ($query) use ($departamentoId) {
                return $query->whereHas('departamentos', function ($query) use ($departamentoId) {
                    $query->where('departamento_id', $departamentoId);
                });
            })
            ->paginate(10); // Cambia a paginate() para paginación, mostrando 10 usuarios por página

        // Si es una solicitud AJAX, devolver la vista completa con la tabla
        if ($request->ajax()) {
            return view('usuarios.index', compact('usuarios', 'departamentos')); // Retorna la vista completa
        }

        return view('usuarios.index', compact('usuarios', 'departamentos')); // Pasar usuarios y departamentos a la vista
    }

    public function create()
    {
        $departamentos = Departamento::all();
        return view('usuarios.create', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string|max:20',
            'departamentos' => 'nullable|array',
            'prefijo' => 'required|string|max:5', // Validar el prefijo
        ]);

        // Eliminar ceros iniciales del número de teléfono
        $telefonoSinCeros = ltrim($request->telefono, '0'); // Eliminar ceros iniciales

        // Combinar el prefijo y el número de teléfono
        $telefonoCompleto = $request->prefijo . $telefonoSinCeros; // El prefijo y el número se combinan

        // Crear el nuevo usuario
        $usuario = Usuario::create($request->only('nombre', 'apellido', 'email') + ['telefono' => $telefonoCompleto]);

        // Asociar departamentos al usuario si existen
        if ($request->has('departamentos')) {
            $usuario->departamentos()->sync($request->departamentos);
        }

        // Obtener las etiquetas para la respuesta
        $usuario->etiquetas = $usuario->departamentos->pluck('nombre');

        // Retornar el usuario creado como respuesta JSON
        return response()->json(['user' => $usuario, 'success' => 'Usuario creado correctamente']);
    }



    public function show(Usuario $usuario)
    {
        return view('usuarios.show', compact('usuario'));
    }

    public function edit(Usuario $usuario)
    {
        $departamentos = Departamento::all(); // Obtener todos los departamentos
        return view('usuarios.edit', compact('usuario', 'departamentos')); // Pasar departamentos al modal
    }

    public function update(Request $request, Usuario $usuario)
    {
        // Validar la solicitud
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'nullable|email',
            'telefono' => 'nullable|string|max:20',
            'departamentos' => 'nullable|array',
            'prefijo' => 'required|string|max:5', // Validar el prefijo
        ]);

        // Obtener el prefijo antiguo
        $antiguoPrefijo = substr($usuario->telefono, 0, strlen($usuario->prefijo));

        // Eliminar ceros iniciales del número de teléfono
        $telefonoSinCeros = ltrim($request->telefono, '0'); // Eliminar ceros iniciales

        // Comprobar si el nuevo prefijo es el mismo que el antiguo
        if ($request->prefijo === $usuario->prefijo) {
            // Si son iguales, mantén el número tal como está
            $telefonoCompleto = $usuario->telefono; // Mantén el teléfono existente
        } else {
            // Si son diferentes, crear el nuevo número de teléfono
            if ($antiguoPrefijo === $usuario->prefijo) {
                $telefonoSinPrefijo = substr($usuario->telefono, strlen($usuario->prefijo)); // Obtener solo el número sin el antiguo prefijo
            } else {
                $telefonoSinPrefijo = $telefonoSinCeros; // Si no tiene el prefijo antiguo, usa el número sin ceros
            }

            // Crear el nuevo número de teléfono
            $telefonoCompleto = $request->prefijo . ltrim($telefonoSinPrefijo, '0'); // Combinar el nuevo prefijo con el número
        }

        // Actualizar los campos del usuario
        $usuario->update($request->only('nombre', 'apellido', 'email'));
        $usuario->telefono = $telefonoCompleto; // Actualizar el teléfono completo
        $usuario->save();

        // Sincronizar departamentos con el usuario
        if ($request->has('departamentos')) {
            $usuario->departamentos()->sync($request->departamentos);
        } else {
            $usuario->departamentos()->sync([]); // Desasociar departamentos si no se seleccionan
        }

        // Cargar los departamentos actualizados
        $usuario->load('departamentos');
        $etiquetas = $usuario->departamentos->pluck('nombre')->toArray(); // Obtener los nombres de los departamentos

        return response()->json([
            'success' => 'Usuario actualizado correctamente.',
            'user' => $usuario,
            'etiquetas' => $etiquetas // Enviar etiquetas actualizadas
        ]);
    }
    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->delete();

        return response()->json(['message' => 'Usuario eliminado correctamente.']);
    }
}
