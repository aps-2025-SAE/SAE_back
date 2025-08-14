<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\EventoController;

// Rota para cadastro de administrador
Route::post('/administrador', [AdministradorController::class, 'store']);

// Rota de login
Route::post('/login', [AdministradorController::class, 'login'])->name('login');

// Rotas de eventos
Route::apiResource('eventos', EventoController::class);

// Rota para cadastrar usuário comum
Route::post('/usuarios', [AdministradorController::class, 'registrarUsuario']);


// Rotas de gerenciamento de usuários - UC11
Route::middleware('auth:sanctum')->prefix('usuarios')->group(function () {
    Route::get('/', [AdministradorController::class, 'listarUsuarios']);
    Route::put('/{id}', [AdministradorController::class, 'editarUsuario']);
    Route::delete('/{id}', [AdministradorController::class, 'removerUsuario']);
});
