<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarioDepartamentoTable extends Migration
{
    public function up()
    {
        Schema::create('usuario_departamento', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('usuario_id')->nullable();
            $table->integer('departamento_id')->nullable();
            
            $table->foreign('usuario_id')->references('id')->on('usuario')->onDelete('set null');
            $table->foreign('departamento_id')->references('id')->on('departamento')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('usuario_departamento');
    }
}

