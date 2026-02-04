<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\numeroController;

Route::middleware(['verificarIp'])->group(function () {
    Route::post('/recibir-numero', [numeroController::class, 'recibirNumero']);
    Route::post('/iniciar-juego', [numeroController::class, 'iniciarJuego']);
    Route::post('/enviar-numero', [numeroController::class, 'enviarNumero']);
    Route::post('/continuar-juego', [numeroController::class, 'continuarJuego']);
    Route::get('/historial', [numeroController::class, 'historial']);
    Route::get('/ultimo-numero', [numeroController::class, 'ultimoNumero']);
    Route::get('/equipo', [numeroController::class, 'listar']);
});

Route::get('/test', function () {
    return response()->json([
        'mensaje' => 'la api jalaaaaaaaaaa',
        'timestamp' => now()->toDateTimeString()
    ]);
});