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
            'datas' => 'required|date|after_or_equal:today',
            'numOfertasDiarias' => 'required|integer',
        ], [
            'datas.after_or_equal' => 'A data precisa ser hoje ou futura!',
        ]);

        //Verifica se já existe um evento com o mesmo tipo e data
        $existeEvento = Evento::where('tipo', $request->tipo)
            ->where('datas', $request->datas)
            ->exists();

        if ($existeEvento) {
            return response()->json([
                'message' => 'Já existe um evento cadastrado com o mesmo tipo e data.'
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
            'datas' => 'required|date|after_or_equal:today',
            'numOfertasDiarias' => 'required|integer',
        ], [
            'datas.after_or_equal' => 'A data precisa ser hoje ou futura!',
        ]);

        $evento = Evento::findOrFail($id);

        // Impede atualizar para uma data já existente em outro evento
        $existeEvento = Evento::where('datas', $request->datas)
            ->where('id', '!=', $id)
            ->exists();

        if ($existeEvento) {
            return response()->json([
                'message' => 'Já existe outro evento cadastrado nesta data.'
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
