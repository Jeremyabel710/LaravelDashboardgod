<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departamento extends Model
{
    protected $table = 'departamento';

    protected $fillable = [
        'nombre',
    ];

    public $timestamps = false; // Desactiva el manejo automático de timestamps

    public function alertas()
    {
        return $this->belongsToMany(Alerta::class, 'alertas_departamento', 'departamento_id', 'alerta_id');
    }

    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'usuario_departamento', 'departamento_id', 'usuario_id');
    }
}
