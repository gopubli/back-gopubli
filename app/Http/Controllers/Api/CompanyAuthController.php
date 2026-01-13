<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CompanyAuthController extends Controller
{
    /**
     * Login da empresa
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $company = Company::where('email', $request->email)->first();

            if (!$company || !Hash::check($request->password, $company->password)) {
                throw ValidationException::withMessages([
                    'email' => ['As credenciais fornecidas estão incorretas.'],
                ]);
            }

            if (!$company->active) {
                throw ValidationException::withMessages([
                    'email' => ['Sua conta está inativa.'],
                ]);
            }

            // Revoga tokens anteriores (opcional)
            $company->tokens()->delete();

            // Cria novo token
            $token = $company->createToken('company-token')->plainTextToken;

            return response()->json([
                'message' => 'Login realizado com sucesso',
                'user' => $company,
                'token' => $token,
                'type' => 'company'
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao realizar login',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Registro de empresa
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:companies',
                'password' => 'required|string|min:8|confirmed',
                'cnpj' => 'nullable|string|unique:companies',
                'phone' => 'nullable|string',
                'address' => 'nullable|string',
            ]);

            $company = Company::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'cnpj' => $request->cnpj,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            // Envia e-mail de verificação (opcional - não bloqueia se falhar)
            try {
                $company->notify(new VerifyEmailNotification('company'));
            } catch (\Exception $e) {
                \Log::warning('Erro ao enviar email de verificação: ' . $e->getMessage());
            }

            $token = $company->createToken('company-token')->plainTextToken;

            return response()->json([
                'message' => 'Empresa registrada com sucesso. Verifique seu e-mail.',
                'user' => $company,
                'token' => $token,
                'type' => 'company'
            ], 201);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao registrar empresa',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
        }
    }

    /**
     * Retorna dados da empresa autenticada
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'type' => 'company'
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

    /**
     * Atualizar perfil da empresa
     */
    public function updateProfile(Request $request)
    {
        try {
            $company = $request->user();

            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:companies,email,' . $company->id,
                'password' => 'sometimes|string|min:8|confirmed',
                'cnpj' => 'sometimes|nullable|string|unique:companies,cnpj,' . $company->id,
                'phone' => 'sometimes|nullable|string',
                'address' => 'sometimes|nullable|string',
            ]);

            $data = $request->except(['password', 'password_confirmation']);
            
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $company->update($data);

            return response()->json([
                'message' => 'Perfil atualizado com sucesso',
                'user' => $company->fresh()
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
