<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class InfluencerAuthController extends Controller
{
    /**
     * Login do influencer
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $influencer = Influencer::where('email', $request->email)->first();

            if (! $influencer || ! Hash::check($request->password, $influencer->password)) {
                throw ValidationException::withMessages([
                    'email' => ['As credenciais fornecidas estão incorretas.'],
                ]);
            }

            if (! $influencer->active) {
                throw ValidationException::withMessages([
                    'email' => ['Sua conta está inativa.'],
                ]);
            }

            // Revoga tokens anteriores (opcional)
            $influencer->tokens()->delete();

            // Cria novo token
            $token = $influencer->createToken('influencer-token')->plainTextToken;

            return response()->json([
                'message' => 'Login realizado com sucesso',
                'user' => $influencer,
                'token' => $token,
                'type' => 'influencer',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao realizar login',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    /**
     * Registro de influencer
     */
    public function register(Request $request)
    {
        try {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:influencers',
                'password' => 'required|string|min:8|confirmed',
                'cpf' => 'nullable|string|unique:influencers',
                'phone' => 'nullable|string',
                'instagram' => 'nullable|string',
                'tiktok' => 'nullable|string',
                'youtube' => 'nullable|string',
                'bio' => 'nullable|string',
                'followers' => 'nullable|integer|min:0',
                'niche' => 'nullable|string|max:255',
            ]);

            $influencer = Influencer::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'cpf' => $request->cpf,
                'phone' => $request->phone,
                'instagram' => $request->instagram,
                'tiktok' => $request->tiktok,
                'youtube' => $request->youtube,
                'bio' => $request->bio,
                'followers' => $request->followers,
                'niche' => $request->niche,
            ]);

            // Envia e-mail de verificação (opcional - não bloqueia se falhar)
            try {
                $influencer->notify(new VerifyEmailNotification('influencer'));
            } catch (\Exception $e) {
                // Log do erro mas não impede o registro
                \Log::warning('Erro ao enviar email de verificação: '.$e->getMessage());
            }

            $token = $influencer->createToken('influencer-token')->plainTextToken;

            return response()->json([
                'message' => 'Influencer registrado com sucesso. Verifique seu e-mail.',
                'user' => $influencer,
                'token' => $token,
                'type' => 'influencer',
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao registrar influencer',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    /**
     * Retorna dados do influencer autenticado
     */
    public function me(Request $request)
    {
   
        try {
            $user = $request->user();
            return response()->json([
                'user' => $user,
                'type' => 'influencer',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao obter dados do usuário',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout realizado com sucesso',
        ]);
    }

    /**
     * Atualizar perfil do influencer
     */
    public function updateProfile(Request $request)
    {
        try {
            $influencer = $request->user();

            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:influencers,email,'.$influencer->id,
                'password' => 'sometimes|string|min:8|confirmed',
                'cpf' => 'sometimes|nullable|string|unique:influencers,cpf,'.$influencer->id,
                'phone' => 'sometimes|nullable|string',
                'instagram' => 'sometimes|nullable|string',
                'tiktok' => 'sometimes|nullable|string',
                'youtube' => 'sometimes|nullable|string',
                'bio' => 'sometimes|nullable|string',
                'followers' => 'sometimes|nullable|integer|min:0',
                'niche' => 'sometimes|nullable|string|max:255',
            ]);

            $data = $request->except(['password', 'password_confirmation']);

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $influencer->update($data);

            return response()->json([
                'message' => 'Perfil atualizado com sucesso',
                'user' => $influencer->fresh(),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar perfil',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
