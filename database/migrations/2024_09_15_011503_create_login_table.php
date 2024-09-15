<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoginTable extends Migration
{
    public function up()
    {
        Schema::create('login', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->integer('usuario_id')->nullable();
            $table->string('username');
            $table->string('password');
            
            $table->foreign('usuario_id')->references('id')->on('usuario')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('login');
    }
}

