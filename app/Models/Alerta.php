<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Alerta extends Model
{
    
    protected $table = 'alertas';

    // Define los atributos que son asignables en masa
    protected $fillable = [
        'nombre',
        'mensaje',
        'fecha_creacion',
        'condicion',
        'fecha_envio_programada',
        'archivo',
    ];

    // Desactiva los timestamps, ya que no se están utilizando
    public $timestamps = false;

    // Define los atributos que deben ser tratados como fechas
    protected $dates = [
        'fecha_creacion',
        'fecha_envio_programada', // Asegúrate de que esté aquí
    ];

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
    
    // Método para actualizar la condición de la alerta a 'enviada'
    public function marcarComoEnviada()
    {
        $this->update([
            'condicion' => 'enviada',
            'fecha_envio_programada' => now(), // Establecer la fecha y hora actual al momento de enviar
        ]);
    }
}
