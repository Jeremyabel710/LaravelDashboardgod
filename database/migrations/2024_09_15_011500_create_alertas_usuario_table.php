<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertasUsuarioTable extends Migration
{
    public function up()
    {
        Schema::create('alertas_usuario', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('alerta_id')->nullable();
            $table->integer('usuario_id')->nullable();
            
            $table->foreign('alerta_id')->references('id')->on('alertas')->onDelete('cascade');
            $table->foreign('usuario_id')->references('id')->on('usuario')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('alertas_usuario');
    }
}

