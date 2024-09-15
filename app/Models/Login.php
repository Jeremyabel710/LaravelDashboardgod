<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    protected $table = 'login';
    public $timestamps = false;

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }
}

