<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JuegoController;

Route::post('/papareicbida', [JuegoController::class, 'paparecibida'])
    ->middleware(['verificar.ip', 'delay.game']);

Route::post('/iniciarjogo', [JuegoController::class, 'empezarjuego']);
Route::get('checarStatus', [JuegoController::class, 'checarestado']);