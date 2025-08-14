<?php

namespace App\Http\Controllers;

use App\Models\Administrador;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdministradorController extends Controller
{
    // Cadastro de novo administrador
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

    public function registrarUsuario(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'tipo_permissao' => 'required|in:usuario,admin',
        ]);

        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tipo_permissao' => $request->tipo_permissao,
        ]);

        return response()->json([
            'mensagem' => 'Usuário cadastrado com sucesso!',
            'usuario' => $usuario
        ], 201);
    }

    // Login com geração de token Sanctum
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

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso!',
            'token' => $token,
        ]);
    }

    // UC11 - Listar usuários
    public function listarUsuarios()
    {
        $usuarios = User::where('tipo_permissao', '!=', 'admin')->get();

        if ($usuarios->isEmpty()) {
            return response()->json(['mensagem' => 'Nenhum usuário comum cadastrado.'], 404);
        }

        return response()->json($usuarios);
    }


    // UC11 - Editar permissão de usuário
    public function editarUsuario(Request $request, $id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json(['mensagem' => 'Usuário não encontrado.'], 404);
        }

        $usuario->update([
            'tipo_permissao' => $request->input('tipo_permissao'),
        ]);

        return response()->json(['mensagem' => 'Dados alterados com sucesso.']);
    }

    // UC11 - Remover usuário
    public function removerUsuario($id)
    {
        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json(['mensagem' => 'Usuário não encontrado.'], 404);
        }

        $usuario->delete();

        return response()->json(['mensagem' => 'Usuário removido com sucesso.']);
    }
}
