<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $table = 'alertas';

    // Define los atributos que son asignables en masa
    protected $fillable = ['mensaje', 'fecha_creacion'];
    
    // Desactiva los timestamps
    public $timestamps = false;

    // Relación muchos a muchos con el modelo Departamento
    public function departamentos()
    {   
        return $this->belongsToMany(Departamento::class, 'alertas_departamento', 'alerta_id', 'departamento_id');
    }

    // Relación muchos a muchos con el modelo Usuario
    public function usuarios()
    {
        return $this->belongsToMany(Usuario::class, 'alertas_usuario', 'alerta_id', 'usuario_id');
    }
}
