<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registrar novo usuário
     */
    public function register(Request $request)
    {
        // Validação dos dados
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Criar usuário
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Criar token
        $token = $user->createToken('auth-token')->plainTextToken;

        // Retornar resposta JSON
        return response()->json([
            'user' => $user,
            'token' => $token
        ], Response::HTTP_CREATED); // 201 Created
    }

    /**
     * Fazer login
     */
    public function login(Request $request)
    {
        // Validação
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar usuário
        $user = User::where('email', $request->email)->first();

        // Verificar credenciais
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        // Criar token
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }

    /**
     * Fazer logout
     */
    public function logout(Request $request)
    {
        // Revogar todos os tokens do usuário
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    /**
     * Obter usuário autenticado
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
