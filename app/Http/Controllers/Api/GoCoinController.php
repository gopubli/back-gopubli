<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoCoinWallet;
use App\Models\GoCoinTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GoCoinController extends Controller
{
    /**
     * Obter saldo da carteira
     */
    public function balance(Request $request)
    {
        $user = $request->user();
        $wallet = $user->getOrCreateWallet();

        return response()->json([
            'balance' => $wallet->balance,
            'total_earned' => $wallet->total_earned,
            'total_spent' => $wallet->total_spent,
        ]);
    }

    /**
     * Histórico de transações
     */
    public function transactions(Request $request)
    {
        $user = $request->user();
        $wallet = $user->getOrCreateWallet();

        $transactions = GoCoinTransaction::where('wallet_id', $wallet->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($transactions);
    }

    /**
     * Filtrar transações por tipo
     */
    public function transactionsByType(Request $request, $type)
    {
        $user = $request->user();
        $wallet = $user->getOrCreateWallet();

        if (!in_array($type, ['credit', 'debit'])) {
            return response()->json([
                'message' => 'Tipo inválido. Use: credit ou debit',
            ], 400);
        }

        $transactions = GoCoinTransaction::where('wallet_id', $wallet->id)
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($transactions);
    }

    /**
     * Filtrar transações por categoria
     */
    public function transactionsByCategory(Request $request, $category)
    {
        $user = $request->user();
        $wallet = $user->getOrCreateWallet();

        $transactions = GoCoinTransaction::where('wallet_id', $wallet->id)
            ->where('category', $category)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($transactions);
    }

    /**
     * Resgatar GO Coins para serviços de marketing
     */
    public function redeem(Request $request)
    {
        $user = $request->user();
        $wallet = $user->getOrCreateWallet();

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:1',
            'service_type' => 'required|string', // trafego_pago, design, consultoria, etc
            'description' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!$wallet->hasSufficientBalance($request->amount)) {
            return response()->json([
                'message' => 'Saldo insuficiente',
                'current_balance' => $wallet->balance,
            ], 400);
        }

        try {
            $transaction = $wallet->debit(
                $request->amount,
                'redemption_' . $request->service_type,
                $request->description
            );

            return response()->json([
                'message' => 'Resgate realizado com sucesso!',
                'transaction' => $transaction,
                'new_balance' => $wallet->fresh()->balance,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Estatísticas da carteira
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        $wallet = $user->getOrCreateWallet();

        $stats = [
            'current_balance' => $wallet->balance,
            'total_earned' => $wallet->total_earned,
            'total_spent' => $wallet->total_spent,
            'total_transactions' => GoCoinTransaction::where('wallet_id', $wallet->id)->count(),
            'total_credits' => GoCoinTransaction::where('wallet_id', $wallet->id)
                ->credits()
                ->sum('amount'),
            'total_debits' => GoCoinTransaction::where('wallet_id', $wallet->id)
                ->debits()
                ->sum('amount'),
            'recent_transactions' => GoCoinTransaction::where('wallet_id', $wallet->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
        ];

        return response()->json($stats);
    }

    /**
     * Listar categorias disponíveis de resgate
     */
    public function redeemCategories()
    {
        $categories = [
            [
                'id' => 'trafego_pago',
                'name' => 'Tráfego Pago',
                'description' => 'Invista em anúncios no Facebook, Instagram, Google Ads',
                'min_amount' => 50,
            ],
            [
                'id' => 'design',
                'name' => 'Design Gráfico',
                'description' => 'Criação de artes, logos, banners',
                'min_amount' => 30,
            ],
            [
                'id' => 'video_editing',
                'name' => 'Edição de Vídeo',
                'description' => 'Edição profissional de vídeos',
                'min_amount' => 40,
            ],
            [
                'id' => 'consultoria',
                'name' => 'Consultoria de Marketing',
                'description' => 'Consultoria especializada em marketing digital',
                'min_amount' => 100,
            ],
            [
                'id' => 'copywriting',
                'name' => 'Copywriting',
                'description' => 'Textos persuasivos para vendas',
                'min_amount' => 25,
            ],
        ];

        return response()->json($categories);
    }
}
