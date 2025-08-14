<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Illuminate\Http\Request;

class PedidoAprovacaoController extends Controller
{
    // GET /api/secretario/pedidos?status=pendente|aprovado|rejeitado
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = Pedido::with([
            'cliente:id,nome_completo,telefone',
            'evento:id,tipo,data_inicio,data_fim,valor'
        ])->orderBy('created_at', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        return response()->json($query->get());
    }

    // GET /api/secretario/pedidos/{id}
    public function show(Pedido $pedido)
{
    $pedido->load([
        'cliente:id,nome_completo,telefone',
        'evento:id,tipo,data_inicio,data_fim,valor'
    ]);

    return response()->json($pedido);
}


    // POST /api/secretario/pedidos/{pedido}/aprovar
    public function aprovar(Request $request, Pedido $pedido)
    {
        if ($pedido->status !== 'pendente') {
            return response()->json(['message' => 'Pedido já processado.'], 409);
        }

        $pedido->status = 'aprovado';
        $pedido->motivo_recusa = null;
        $pedido->secretario_id = $request->user()->id; // quem aprovou
        $pedido->avaliado_em = now();
        $pedido->save();

        return response()->json([
            'message' => 'Pedido aprovado com sucesso.',
            'pedido'  => $pedido
        ]);
    }

    // Alias para casar com a rota /recusar
    // POST /api/secretario/pedidos/{pedido}/recusar
    public function recusar(Request $request, Pedido $pedido)
    {
        return $this->rejeitar($request, $pedido);
    }

    // Implementação da rejeição (mantida por clareza semântica)
    public function rejeitar(Request $request, Pedido $pedido)
    {
        $request->validate([
            'motivo_recusa' => 'required|string|max:1000',
        ]);

        if ($pedido->status !== 'pendente') {
            return response()->json(['message' => 'Pedido já processado.'], 409);
        }

        $pedido->status = 'rejeitado'; // enum existente: pendente/aprovado/rejeitado
        $pedido->motivo_recusa = $request->motivo_recusa;
        $pedido->secretario_id = $request->user()->id; // quem rejeitou
        $pedido->avaliado_em = now();
        $pedido->save();

        return response()->json([
            'message' => 'Pedido rejeitado com sucesso.',
            'pedido'  => $pedido
        ]);
    }
}
