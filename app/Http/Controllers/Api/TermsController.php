<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TermsAcceptance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TermsController extends Controller
{
    /**
     * Aceitar termo de confidencialidade
     */
    public function acceptConfidentiality(Request $request)
    {
        $user = $request->user();

        // Verificar se já aceitou
        if ($user->hasAcceptedTerm('confidentiality')) {
            return response()->json([
                'message' => 'Você já aceitou o Termo de Confidencialidade',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'accepted' => 'required|boolean|accepted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Você deve aceitar o termo para continuar',
                'errors' => $validator->errors(),
            ], 422);
        }

        TermsAcceptance::recordAcceptance($user, 'confidentiality', '1.0', $request);

        return response()->json([
            'message' => 'Termo de Confidencialidade aceito com sucesso',
        ]);
    }

    /**
     * Aceitar política de privacidade
     */
    public function acceptPrivacyPolicy(Request $request)
    {
        $user = $request->user();

        if ($user->hasAcceptedTerm('privacy_policy')) {
            return response()->json([
                'message' => 'Você já aceitou a Política de Privacidade',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'accepted' => 'required|boolean|accepted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Você deve aceitar a política para continuar',
                'errors' => $validator->errors(),
            ], 422);
        }

        TermsAcceptance::recordAcceptance($user, 'privacy_policy', '1.0', $request);

        return response()->json([
            'message' => 'Política de Privacidade aceita com sucesso',
        ]);
    }

    /**
     * Aceitar termos de uso
     */
    public function acceptTermsOfUse(Request $request)
    {
        $user = $request->user();

        if ($user->hasAcceptedTerm('terms_of_use')) {
            return response()->json([
                'message' => 'Você já aceitou os Termos de Uso',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'accepted' => 'required|boolean|accepted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Você deve aceitar os termos para continuar',
                'errors' => $validator->errors(),
            ], 422);
        }

        TermsAcceptance::recordAcceptance($user, 'terms_of_use', '1.0', $request);

        return response()->json([
            'message' => 'Termos de Uso aceitos com sucesso',
        ]);
    }

    /**
     * Verificar status de aceite dos termos
     */
    public function status(Request $request)
    {
        $user = $request->user();

        $status = [
            'confidentiality' => $user->hasAcceptedTerm('confidentiality'),
            'privacy_policy' => $user->hasAcceptedTerm('privacy_policy'),
            'terms_of_use' => $user->hasAcceptedTerm('terms_of_use'),
        ];

        return response()->json($status);
    }

    /**
     * Histórico de aceites
     */
    public function history(Request $request)
    {
        $user = $request->user();

        $acceptances = TermsAcceptance::where('user_type', get_class($user))
            ->where('user_id', $user->id)
            ->orderBy('accepted_at', 'desc')
            ->get();

        return response()->json($acceptances);
    }

    /**
     * Obter texto do termo de confidencialidade
     */
    public function getConfidentialityText()
    {
        $text = "
# TERMO DE CONFIDENCIALIDADE - GO PUBLI

Ao aceitar este termo, você concorda com as seguintes condições:

1. **Proteção de Dados**: Todos os dados compartilhados na plataforma são confidenciais.

2. **Proibição de Contato Externo**: É PROIBIDO o contato direto entre empresas e influenciadores fora da plataforma GO Publi antes da confirmação e pagamento da campanha.

3. **Segurança**: Dados pessoais como e-mail, telefone e redes sociais só serão compartilhados após a empresa selecionar o influencer e confirmar o pagamento.

4. **Penalidades**: A violação deste termo pode resultar em:
   - Suspensão da conta
   - Bloqueio permanente da plataforma
   - Medidas legais cabíveis

5. **Compromisso**: Ao usar a GO Publi, você se compromete a respeitar todas as regras e manter a confidencialidade das informações.

Versão 1.0 - Janeiro 2026
        ";

        return response()->json([
            'term_type' => 'confidentiality',
            'version' => '1.0',
            'text' => trim($text),
        ]);
    }
}
