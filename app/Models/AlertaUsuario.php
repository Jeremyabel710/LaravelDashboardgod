<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertaUsuario extends Model
{
    protected $table = 'alertas_usuario';
    public $timestamps = false;

    protected $fillable = ['alerta_id', 'usuario_id'];

    public function alerta()
    {
        return $this->belongsTo(Alerta::class, 'alerta_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
