<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdministradorController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required',
            'email' => 'required|email|unique:administrador,email',
            'login' => 'required|unique:administrador,login',
            'senha' => 'required|min:6',
        ]);

        $admin = Administrador::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'login' => $request->login,
            'senha' => Hash::make($request->senha),
        ]);

        return response()->json([
            'message' => 'Administrador cadastrado com sucesso!',
            'data' => $admin
        ], 201);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'senha' => 'required|string',
        ]);

        $admin = Administrador::where('login', $request->login)->first();

        if (!$admin || !Hash::check($request->senha, $admin->senha)) {
            return response()->json(['message' => 'Credenciais inválidas.'], 401);
        }

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'administrador' => $admin,
        ]);
    }
}
