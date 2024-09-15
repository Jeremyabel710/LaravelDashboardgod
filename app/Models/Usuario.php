<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
    protected $table = 'usuario';

    // Desactivar el manejo automático de timestamps
    public $timestamps = false;

    // Permitir la asignación en masa para los campos especificados
    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
    ];

    public function departamentos()
    {
        return $this->belongsToMany(Departamento::class, 'usuario_departamento', 'usuario_id', 'departamento_id');
    }

    public function alertas()
    {
        return $this->belongsToMany(Alerta::class, 'alertas_usuario', 'usuario_id', 'alerta_id');
    }

    public function login()
    {
        return $this->hasOne(Login::class);
    }
}
