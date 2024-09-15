<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartamentoTable extends Migration
{
    public function up()
    {
        Schema::create('departamento', function (Blueprint $table) {
            $table->integer('id')->primary()->autoIncrement(); // Definir 'id' como entero auto-incrementable
            $table->string('nombre');
        });
    }

    public function down()
    {
        Schema::dropIfExists('departamento');
    }
}
