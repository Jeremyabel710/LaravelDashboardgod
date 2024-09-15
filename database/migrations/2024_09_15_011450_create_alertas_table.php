<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlertasTable extends Migration
{
    public function up()
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->text('mensaje');
            $table->dateTime('fecha_creacion')->default(now());
        });
    }

    public function down()
    {
        Schema::dropIfExists('alertas');
    }
}

