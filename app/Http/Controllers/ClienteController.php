<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ClienteController extends Controller
{
    public function inicio()
    {
        return response()->json(['message' => 'Bem-vindo à página inicial do Cliente']);
    }

    public function todos()
    {
        $eventos = Evento::all();

        if ($eventos->isEmpty()) {
            return response()->json(['message' => 'Não há eventos disponíveis.'], 404);
        }

        return response()->json($eventos);
    }

    public function disponiveis()
    {
    $hoje = now()->toDateString();

    $eventosDisponiveis = Evento::where('numOfertasDiarias', '>=', 1)
        ->where('data_fim', '>', $hoje)
        ->get();

    if ($eventosDisponiveis->isEmpty()) {
        return response()->json(['message' => 'Não há eventos disponíveis.'], 404);
    }

    return response()->json($eventosDisponiveis);
    }
}
