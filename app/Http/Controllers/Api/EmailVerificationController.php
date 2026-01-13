<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Administrator;
use App\Models\Company;
use App\Models\Influencer;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class EmailVerificationController extends Controller
{
    /**
     * Envia e-mail de verificação
     */
    public function sendVerificationEmail(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'E-mail já verificado'
            ], 400);
        }

        $userType = $this->getUserType($user);
        $user->notify(new VerifyEmailNotification($userType));

        return response()->json([
            'message' => 'E-mail de verificação enviado com sucesso'
        ]);
    }

    /**
     * Verifica o e-mail através do link
     */
    public function verify(Request $request, $type, $id, $hash)
    {
        $config = $this->getUserConfig($type);
        if (!$config) {
            return response()->json(['message' => 'Tipo de usuário inválido'], 400);
        }

        // Verifica a assinatura da URL
        if (!$request->hasValidSignature()) {
            return response()->json([
                'message' => 'Link de verificação inválido ou expirado'
            ], 400);
        }

        $user = $config['model']::findOrFail($id);

        // Verifica o hash
        if (sha1($user->getEmailForVerification()) !== $hash) {
            return response()->json([
                'message' => 'Link de verificação inválido'
            ], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'E-mail já verificado anteriormente'
            ]);
        }

        $user->markEmailAsVerified();

        return response()->json([
            'message' => 'E-mail verificado com sucesso!',
            'user' => $user
        ]);
    }

    /**
     * Verifica status de verificação do e-mail
     */
    public function checkVerification(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'verified' => $user->hasVerifiedEmail(),
            'email' => $user->email,
        ]);
    }

    /**
     * Determina o tipo de usuário
     */
    private function getUserType($user)
    {
        if ($user instanceof Administrator) {
            return 'admin';
        } elseif ($user instanceof Company) {
            return 'company';
        } elseif ($user instanceof Influencer) {
            return 'influencer';
        }
        return 'user';
    }

    /**
     * Retorna configuração baseada no tipo de usuário
     */
    private function getUserConfig($userType)
    {
        $configs = [
            'admin' => ['model' => Administrator::class],
            'company' => ['model' => Company::class],
            'influencer' => ['model' => Influencer::class],
        ];

        return $configs[$userType] ?? null;
    }
}
