<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioDepartamento extends Model
{
    protected $table = 'usuario_departamento';

    // Desactivar timestamps automáticos si la tabla no los tiene
    public $timestamps = false;

    // Definir los campos que pueden ser asignados en masa (fillable)
    protected $fillable = [
        'usuario_id',
        'departamento_id',
    ];  

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }
}
