<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Registrar el comando que ya existe en App\Console\Commands\EnviarAlertas.php
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar el comando para que se ejecute cada minuto
Schedule::command('alertas:enviar')->everyMinute();
