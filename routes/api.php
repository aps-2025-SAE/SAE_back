<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ClienteAuthController;

Route::post('/administrador', [\App\Http\Controllers\AdministradorController::class, 'store']);

Route::post('/login', [AdministradorController::class, 'login']);

Route::apiResource('eventos', EventoController::class);

Route::prefix('cliente')->group(function () {
    Route::get('/inicio', [ClienteController::class, 'inicio']);
    Route::get('/eventos', [ClienteController::class, 'todos']);
    Route::get('/eventos/disponiveis', [ClienteController::class, 'disponiveis']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cliente/pedido', [PedidoController::class, 'store']);
});

Route::post('/cliente/register', [ClienteAuthController::class, 'register']);
Route::post('/cliente/login', [ClienteAuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/cliente/logout', [ClienteAuthController::class, 'logout']);
    Route::post('/cliente/pedido', [PedidoController::class, 'store']);
    Route::get('/cliente/agendamentos', [PedidoController::class, 'index']);
});

