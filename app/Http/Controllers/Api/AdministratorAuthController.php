<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Administrator;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdministratorAuthController extends Controller
{
    /**
     * Login do administrador
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $admin = Administrator::where('email', $request->email)->first();

        if (!$admin || !Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'email' => ['As credenciais fornecidas estão incorretas.'],
            ]);
        }

        if (!$admin->active) {
            throw ValidationException::withMessages([
                'email' => ['Sua conta está inativa.'],
            ]);
        }

        // Revoga tokens anteriores (opcional)
        $admin->tokens()->delete();

        // Cria novo token
        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'message' => 'Login realizado com sucesso',
            'user' => $admin,
            'token' => $token,
            'type' => 'administrator'
        ]);
    }

    /**
     * Registro de administrador
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:administrators',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $admin = Administrator::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Envia e-mail de verificação
        $admin->notify(new VerifyEmailNotification('admin'));

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'message' => 'Administrador registrado com sucesso. Verifique seu e-mail.',
            'user' => $admin,
            'token' => $token,
            'type' => 'administrator'
        ], 201);
    }

    /**
     * Retorna dados do administrador autenticado
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'type' => 'administrator'
        ]);
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso'
        ]);
    }
}
