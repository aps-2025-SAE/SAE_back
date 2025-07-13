<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\EventoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/teste-banco', function () {
    try {
        DB::connection()->getPdo();
        return "✅ Conectado com sucesso ao banco: " . DB::connection()->getDatabaseName();
    } catch (\Exception $e) {
        return "❌ Erro na conexão: " . $e->getMessage();
    }
});

Route::resource('eventos', EventoController::class); // Registra todas as rotas RESTful do CRUD
