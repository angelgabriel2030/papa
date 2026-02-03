<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\numeroController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['App\Http\Middleware\ValidarIp'])->group(function () {
    Route::post('/numero/enviar', [numeroController::class, 'enviar']);
    Route::get('/numero/recibir', [numeroController::class, 'recibir']);
    Route::get('/numero/historial', [numeroController::class, 'historial']);
});
