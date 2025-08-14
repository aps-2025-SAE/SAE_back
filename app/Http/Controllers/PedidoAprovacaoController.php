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

    public function update(Request $request, \App\Models\Pedido $pedido)
    {
    // Campos que o secretário pode editar
    $validated = $request->validate([
        'data_solicitada'     => 'sometimes|date|after_or_equal:today',
        'horario'             => 'sometimes|date_format:H:i',
        'endereco'            => 'sometimes|string|max:255',
        'quantidade_pessoas'  => 'sometimes|integer|min:1',
        // 'tipo' => 'sometimes|string|max:255', // só se existir no schema
    ]);

    // Não editar pedidos rejeitados
    if ($pedido->status === 'rejeitado') {
        return response()->json([
            'message' => 'Não é possível editar pedidos rejeitados.'
        ], 422);
    }

    // Aplique mudanças
    $pedido->fill($validated);

    // Detectar mudanças críticas de forma confiável
    $criticos = ['data_solicitada', 'horario', 'endereco', 'quantidade_pessoas'];
    $houveMudancaCritica = false;
    foreach ($criticos as $campo) {
        if ($pedido->isDirty($campo)) { // compara valor atual com o original do banco
            $houveMudancaCritica = true;
            break;
        }
    }

    // Se estava aprovado e mudou algo crítico, volta para pendente e limpa avaliação
    if ($pedido->status === 'aprovado' && $houveMudancaCritica) {
        $pedido->status = 'pendente';
        $pedido->motivo_recusa = null;
        $pedido->secretario_id = null;
        $pedido->avaliado_em = null;
    }

    $pedido->save();

    // Opcional: retornar com relações já carregadas
    $pedido->load([
        'cliente:id,nome_completo,telefone',
        'evento:id,tipo,data_inicio,data_fim,valor'
    ]);

    return response()->json([
        'message' => 'Solicitação alterada com sucesso.',
        'pedido'  => $pedido
    ]);
    }

    // NOVO: DELETE /api/secretario/pedidos/{pedido}
    public function destroy(Request $request, Pedido $pedido)
    {
        $pedido->delete();

        return response()->json([
            'message' => 'Solicitação removida com sucesso.'
        ]);
    }
}
