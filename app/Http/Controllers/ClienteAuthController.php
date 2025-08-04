<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use Illuminate\Support\Facades\Hash;

class ClienteAuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'nome_completo' => 'required|string',
            'telefone' => 'required|string|unique:clientes,telefone',
            'password' => 'required|string|min:6',
        ]);

        $cliente = Cliente::create([
            'nome_completo' => $request->nome_completo,
            'telefone' => $request->telefone,
            'password' => Hash::make($request->password),
        ]);

        $token = $cliente->createToken('token-cliente')->plainTextToken;

        return response()->json([
            'token' => $token,
            'cliente' => $cliente
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'telefone' => 'required|string',
            'password' => 'required|string'
        ]);

        $cliente = Cliente::where('telefone', $request->telefone)->first();

        if (!$cliente || !Hash::check($request->password, $cliente->password)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $token = $cliente->createToken('token-cliente')->plainTextToken;

        return response()->json([
            'token' => $token,
            'cliente' => $cliente
        ], 200);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }
}
