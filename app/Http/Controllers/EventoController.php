<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventoController extends Controller
{
    public function index()
    {
        return response()->json(Evento::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string',
            'valor' => 'required|numeric',
            'descricao' => 'required|string',
            'data_inicio' => 'required|date|after_or_equal:today',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'numOfertasDiarias' => 'required|integer',
        ], [
            'data_inicio.after_or_equal' => 'A data de início precisa ser hoje ou futura!',
            'data_fim.after_or_equal' => 'A data final precisa ser igual ou maior que a data de início!',
        ]);

        // Verifica se já existe evento com o mesmo tipo e intervalo que conflita
        $existeEvento = Evento::where('tipo', $request->tipo)
            ->where(function($query) use ($request) {
                $query->whereBetween('data_inicio', [$request->data_inicio, $request->data_fim])
                      ->orWhereBetween('data_fim', [$request->data_inicio, $request->data_fim])
                      ->orWhere(function($q) use ($request) {
                          $q->where('data_inicio', '<=', $request->data_inicio)
                            ->where('data_fim', '>=', $request->data_fim);
                      });
            })
            ->exists();

        if ($existeEvento) {
            return response()->json([
                'message' => 'Já existe um evento cadastrado com o mesmo tipo e intervalo de datas que conflita.'
            ], 422);
        }

        $evento = Evento::create($request->all());

        return response()->json([
            'message' => 'Evento cadastrado com sucesso!',
            'data' => $evento
        ], 201);
    }

    public function show($id)
    {
        $evento = Evento::findOrFail($id);
        return response()->json($evento);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tipo' => 'required|string',
            'valor' => 'required|numeric',
            'descricao' => 'required|string',
            'data_inicio' => 'required|date|after_or_equal:today',
            'data_fim' => 'required|date|after_or_equal:data_inicio',
            'numOfertasDiarias' => 'required|integer',
        ], [
            'data_inicio.after_or_equal' => 'A data de início precisa ser hoje ou futura!',
            'data_fim.after_or_equal' => 'A data final precisa ser igual ou maior que a data de início!',
        ]);

        $evento = Evento::findOrFail($id);

        // Impede conflito de datas com outros eventos do mesmo tipo
        $existeEvento = Evento::where('tipo', $request->tipo)
            ->where('id', '!=', $id)
            ->where(function($query) use ($request) {
                $query->whereBetween('data_inicio', [$request->data_inicio, $request->data_fim])
                      ->orWhereBetween('data_fim', [$request->data_inicio, $request->data_fim])
                      ->orWhere(function($q) use ($request) {
                          $q->where('data_inicio', '<=', $request->data_inicio)
                            ->where('data_fim', '>=', $request->data_fim);
                      });
            })
            ->exists();

        if ($existeEvento) {
            return response()->json([
                'message' => 'Já existe outro evento cadastrado no intervalo de datas informado.'
            ], 422);
        }

        $evento->update($request->all());

        return response()->json([
            'message' => 'Evento atualizado com sucesso!',
            'data' => $evento
        ]);
    }

    public function destroy($id)
    {
        $evento = Evento::findOrFail($id);
        $evento->delete();

        return response()->json(['message' => 'Evento deletado com sucesso!']);
    }
}
