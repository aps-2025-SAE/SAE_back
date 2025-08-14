<?php

namespace App\Http\Controllers;

use App\Models\Secretario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SecretarioAuthController extends Controller
{
    // Registro (útil em desenvolvimento; em produção pode ser desabilitado)
    public function register(Request $request)
    {
        $request->validate([
            'nome'  => 'required|string|max:255',
            'login' => 'required|string|max:255|unique:secretarios,login',
            'senha' => 'required|string|min:6',
        ]);

        $secretario = Secretario::create([
            'nome'  => $request->nome,
            'login' => $request->login,
            'senha' => Hash::make($request->senha),
        ]);

        $token = $secretario->createToken('token-secretario')->plainTextToken;

        return response()->json([
            'token' => $token,
            'secretario' => $secretario,
        ], 201);
    }

    // LOGIN via Sanctum: valida credenciais manualmente e gera token
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'senha' => 'required|string',
        ]);

        $secretario = Secretario::where('login', $request->login)->first();

        if (!$secretario || !Hash::check($request->senha, $secretario->senha)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        // Gera token Sanctum
        $token = $secretario->createToken('token-secretario')->plainTextToken;

        return response()->json([
            'token' => $token,
            'secretario' => $secretario,
        ]);
    }

    // LOGOUT: revoga todos os tokens do secretário autenticado
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logout realizado com sucesso.']);
    }
}
