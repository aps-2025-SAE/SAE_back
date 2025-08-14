<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pedido;
use Illuminate\Support\Facades\Auth;

class PedidoController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'message' => 'Acesso rápido. Informe nome completo e telefone.'
            ], 401);
        }

        $request->validate([
            'evento_id' => 'required|exists:eventos,id',
            'data_solicitada' => 'required|date',
            'horario' => 'required|string',
            'endereco' => 'required|string',
            'quantidade_pessoas' => 'required|integer|min:1'
        ]);

        $pedido = Pedido::create([
            'cliente_id' => Auth::id(),
            'evento_id' => $request->evento_id,
            'data_solicitada' => $request->data_solicitada,
            'horario' => $request->horario,
            'endereco' => $request->endereco,
            'quantidade_pessoas' => $request->quantidade_pessoas,
        ]);

        return response()->json([
            'message' => 'Pedido concluído com sucesso. Aguarde aprovação.',
            'data' => $pedido
        ], 201);
    }
    
    public function index(Request $request)
    {
        $cliente = $request->user();

        $agendamentos = $cliente->pedidos()->with('evento')->get();

        if ($agendamentos->isEmpty()) {
            return response()->json([
                'message' => 'Não há eventos disponíveis.'
            ], 200);
        }

        return response()->json([
            'agendamentos' => $agendamentos
        ], 200);
    }
}
