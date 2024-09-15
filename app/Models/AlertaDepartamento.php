<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertaDepartamento extends Model
{
    protected $table = 'alertas_departamento';
    public $timestamps = false;

    protected $fillable = ['alerta_id', 'departamento_id'];

    public function alerta()
    {
        return $this->belongsTo(Alerta::class, 'alerta_id');
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }
}
