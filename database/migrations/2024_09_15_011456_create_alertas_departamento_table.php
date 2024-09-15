<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertasDepartamentoTable extends Migration
{
    public function up()
    {
        Schema::create('alertas_departamento', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('alerta_id');
            $table->integer('departamento_id');
            
            $table->foreign('alerta_id')->references('id')->on('alertas')->onDelete('cascade');
            $table->foreign('departamento_id')->references('id')->on('departamento')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('alertas_departamento');
    }
}

