<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Departamento;
use Illuminate\Http\Request;

class UsuarioDepartamentoController extends Controller
{
    public function index()
    {
        $usuarios = Usuario::with('departamentos')->get();
        return view('usuariosdepartamentos.index', compact('usuarios'));
    }

    public function create()
    {
        $departamentos = Departamento::all();
        return view('usuariosdepartamentos.create', compact('departamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'departamentos' => 'required|array',
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
        ]);

        $usuario->departamentos()->sync($request->departamentos);

        return redirect()->route('usuariosdepartamentos.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function edit($id)
    {
        $usuario = Usuario::with('departamentos')->findOrFail($id);
        $departamentos = Departamento::all();
        return view('usuariosdepartamentos.edit', compact('usuario', 'departamentos'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'departamentos' => 'required|array',
        ]);

        $usuario = Usuario::findOrFail($id);
        $usuario->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
        ]);

        $usuario->departamentos()->sync($request->departamentos);

        return redirect()->route('usuariosdepartamentos.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        $usuario->departamentos()->detach();
        $usuario->delete();
        return redirect()->route('usuariosdepartamentos.index')->with('success', 'Usuario eliminado correctamente.');
    }

    public function associate()
    {
        $usuarios = Usuario::all();
        $departamentos = Departamento::all();
        return view('usuariosdepartamentos.associate', compact('usuarios', 'departamentos'));
    }

    public function storeAssociation(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuario,id',
            'departamentos' => 'required|array',
        ]);

        $usuario = Usuario::findOrFail($request->usuario_id);
        $usuario->departamentos()->sync($request->departamentos);

        return redirect()->route('usuariosdepartamentos.index')->with('success', 'Departamentos asociados correctamente.');
    }
}
