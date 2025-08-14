<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ClienteAuthController;
use App\Http\Controllers\SecretarioAuthController;
use App\Http\Controllers\PedidoAprovacaoController;
use App\Http\Middleware\SecretarioMiddleware;

Route::post('/administrador', [AdministradorController::class, 'store']);
Route::post('/login', [AdministradorController::class, 'login']);

Route::apiResource('eventos', EventoController::class);

Route::prefix('cliente')->group(function () {
    Route::get('/inicio', [ClienteController::class, 'inicio']);
    Route::get('/eventos', [ClienteController::class, 'todos']);
    Route::get('/eventos/disponiveis', [ClienteController::class, 'disponiveis']);

    Route::post('/register', [ClienteAuthController::class, 'register']);
    Route::post('/login', [ClienteAuthController::class, 'login']);
    Route::post('/cadastro-rapido', [ClienteAuthController::class, 'cadastroRapido']);
});

Route::middleware('auth:sanctum')->prefix('cliente')->group(function () {
    Route::post('/logout', [ClienteAuthController::class, 'logout']);
    Route::post('/pedido', [PedidoController::class, 'store']);
    Route::get('/agendamentos', [PedidoController::class, 'index']);
});

Route::prefix('secretario')->group(function () {
    Route::post('/register', [SecretarioAuthController::class, 'register']);
    Route::post('/login', [SecretarioAuthController::class, 'login']);

    Route::middleware(['auth:sanctum', SecretarioMiddleware::class])->group(function () {
        Route::get('/pedidos', [PedidoAprovacaoController::class, 'index']);
        Route::get('/pedidos/{pedido}', [PedidoAprovacaoController::class, 'show']);
        Route::post('/pedidos/{pedido}/aprovar', [PedidoAprovacaoController::class, 'aprovar']);
        Route::post('/pedidos/{pedido}/recusar', [PedidoAprovacaoController::class, 'recusar']);
    });
});
