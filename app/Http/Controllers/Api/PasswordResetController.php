<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Administrator;
use App\Models\Company;
use App\Models\Influencer;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Solicita reset de senha (envia e-mail)
     */
    public function forgotPassword(Request $request, $userType)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        // Determina model e tabela baseado no tipo
        $config = $this->getUserConfig($userType);
        if (!$config) {
            return response()->json(['message' => 'Tipo de usuário inválido'], 400);
        }

        $user = $config['model']::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Se o e-mail existir em nossa base, você receberá as instruções para redefinir sua senha.'
            ], 200);
        }

        // Remove tokens antigos
        DB::table($config['table'])->where('email', $request->email)->delete();

        // Cria novo token
        $token = Str::random(64);

        DB::table($config['table'])->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        // Envia e-mail
        $user->notify(new ResetPasswordNotification($token, $userType));

        return response()->json([
            'message' => 'Se o e-mail existir em nossa base, você receberá as instruções para redefinir sua senha.',
            'token' => config('app.debug') ? $token : null, // Remove em produção
        ]);
    }

    /**
     * Reseta a senha
     */
    public function resetPassword(Request $request, $userType)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $config = $this->getUserConfig($userType);
        if (!$config) {
            return response()->json(['message' => 'Tipo de usuário inválido'], 400);
        }

        // Busca token
        $passwordReset = DB::table($config['table'])
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset) {
            return response()->json(['message' => 'Token inválido ou expirado'], 400);
        }

        // Verifica se o token está correto
        if (!Hash::check($request->token, $passwordReset->token)) {
            return response()->json(['message' => 'Token inválido'], 400);
        }

        // Verifica se o token não expirou (60 minutos)
        if (Carbon::parse($passwordReset->created_at)->addMinutes(60)->isPast()) {
            DB::table($config['table'])->where('email', $request->email)->delete();
            return response()->json(['message' => 'Token expirado'], 400);
        }

        // Atualiza senha
        $user = $config['model']::where('email', $request->email)->first();
        
        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Remove token usado
        DB::table($config['table'])->where('email', $request->email)->delete();

        // Revoga todos os tokens de acesso
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Senha redefinida com sucesso! Por favor, faça login com sua nova senha.',
        ]);
    }

    /**
     * Retorna configuração baseada no tipo de usuário
     */
    private function getUserConfig($userType)
    {
        $configs = [
            'admin' => [
                'model' => Administrator::class,
                'table' => 'administrator_password_resets',
            ],
            'company' => [
                'model' => Company::class,
                'table' => 'company_password_resets',
            ],
            'influencer' => [
                'model' => Influencer::class,
                'table' => 'influencer_password_resets',
            ],
        ];

        return $configs[$userType] ?? null;
    }
}
