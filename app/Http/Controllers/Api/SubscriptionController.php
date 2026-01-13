<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * Obter assinatura da empresa
     */
    public function show(Request $request)
    {
        $company = $request->user();
        $subscription = $company->subscription;

        if (!$subscription) {
            return response()->json([
                'message' => 'Nenhuma assinatura encontrada',
                'has_subscription' => false,
            ], 404);
        }

        return response()->json([
            'subscription' => $subscription,
            'is_overdue' => $subscription->isOverdue(),
            'days_overdue' => $subscription->calculateDaysOverdue(),
        ]);
    }

    /**
     * Criar assinatura para a empresa
     */
    public function create(Request $request)
    {
        $company = $request->user();

        // Verificar se já tem assinatura
        if ($company->subscription) {
            return response()->json([
                'message' => 'Você já possui uma assinatura',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'monthly_amount' => 'required|numeric|min:200', // Mínimo R$ 200
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors(),
            ], 422);
        }

        $subscription = Subscription::create([
            'company_id' => $company->id,
            'monthly_amount' => $request->monthly_amount,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Assinatura criada. Proceda com o pagamento para ativá-la.',
            'subscription' => $subscription,
        ], 201);
    }

    /**
     * Confirmar pagamento da assinatura (simular integração)
     */
    public function confirmPayment(Request $request)
    {
        $company = $request->user();
        $subscription = $company->subscription;

        if (!$subscription) {
            return response()->json([
                'message' => 'Nenhuma assinatura encontrada',
            ], 404);
        }

        if ($subscription->status === 'active') {
            return response()->json([
                'message' => 'Sua assinatura já está ativa',
            ], 400);
        }

        // Aqui seria a integração com Asaas ou outro gateway
        // Por enquanto, simular pagamento confirmado

        $subscription->activate();

        return response()->json([
            'message' => 'Pagamento confirmado! Sua assinatura está ativa.',
            'subscription' => $subscription->fresh(),
        ]);
    }

    /**
     * Renovar assinatura
     */
    public function renew(Request $request)
    {
        $company = $request->user();
        $subscription = $company->subscription;

        if (!$subscription) {
            return response()->json([
                'message' => 'Nenhuma assinatura encontrada',
            ], 404);
        }

        // Simular pagamento da renovação
        $subscription->renew();

        return response()->json([
            'message' => 'Assinatura renovada com sucesso!',
            'subscription' => $subscription->fresh(),
        ]);
    }

    /**
     * Cancelar assinatura
     */
    public function cancel(Request $request)
    {
        $company = $request->user();
        $subscription = $company->subscription;

        if (!$subscription) {
            return response()->json([
                'message' => 'Nenhuma assinatura encontrada',
            ], 404);
        }

        $subscription->update([
            'status' => 'cancelled',
        ]);

        return response()->json([
            'message' => 'Assinatura cancelada',
            'subscription' => $subscription->fresh(),
        ]);
    }

    /**
     * Verificar status da assinatura
     */
    public function status(Request $request)
    {
        $company = $request->user();
        $subscription = $company->subscription;

        if (!$subscription) {
            return response()->json([
                'has_subscription' => false,
                'is_active' => false,
                'is_overdue' => false,
            ]);
        }

        return response()->json([
            'has_subscription' => true,
            'is_active' => $subscription->status === 'active',
            'is_overdue' => $subscription->isOverdue(),
            'days_overdue' => $subscription->calculateDaysOverdue(),
            'status' => $subscription->status,
            'next_billing_date' => $subscription->next_billing_date,
            'monthly_amount' => $subscription->monthly_amount,
        ]);
    }

    /**
     * Histórico de pagamentos (simplificado)
     */
    public function paymentHistory(Request $request)
    {
        $company = $request->user();
        $subscription = $company->subscription;

        if (!$subscription) {
            return response()->json([
                'message' => 'Nenhuma assinatura encontrada',
            ], 404);
        }

        // Por enquanto, retornar apenas informações básicas
        // Futuramente, criar uma tabela de payment_history
        return response()->json([
            'subscription' => $subscription,
            'message' => 'Histórico detalhado de pagamentos será implementado em breve',
        ]);
    }
}
