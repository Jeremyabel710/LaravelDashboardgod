<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\AlertasController;
use App\Http\Controllers\UsuarioDepartamentoController;

Route::get('/', function () {
    return view('auth.login');
});

// Rutas protegidas por autenticación
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Redirige a la página de índice de alertas después del login
    Route::get('/dashboard', function () {
        return redirect()->route('usuarios.index');
    })->name('dashboard');  

    // Rutas para el controlador de departamentos
    Route::resource('departamentos', DepartamentoController::class);

    // En tu archivo web.php
    Route::post('/alertas/enviar/{id}', [AlertasController::class, 'enviar'])->name('alertas.enviar');

    // Rutas para el controlador de usuarios
    Route::resource('usuarios', UsuarioController::class);

    // Rutas para el controlador de alertas
    Route::resource('alertas', AlertasController::class);
    Route::post('/alertas', [AlertasController::class, 'store'])->name('alertas.store');


    // Ruta para enviar alertas a departamentos o usuarios
    Route::post('alertas/enviar/{id}', [AlertasController::class, 'enviar'])->name('alertas.enviar');

    // Rutas para el controlador de Usuario-Departamento
    Route::resource('usuariosdepartamentos', UsuarioDepartamentoController::class)
        ->except(['show']); // Excluir el método show

    // Rutas adicionales para asociar usuarios y departamentos
    Route::get('usuariosdepartamentos/associate', [UsuarioDepartamentoController::class, 'associate'])->name('usuariosdepartamentos.associate');
    Route::post('usuariosdepartamentos/storeAssociation', [UsuarioDepartamentoController::class, 'storeAssociation'])->name('usuariosdepartamentos.storeAssociation');
});
