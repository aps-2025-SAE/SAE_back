<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\EventoController;

Route::post('/administrador', [\App\Http\Controllers\AdministradorController::class, 'store']);

Route::post('/login', [AdministradorController::class, 'login']);

Route::apiResource('eventos', EventoController::class);
