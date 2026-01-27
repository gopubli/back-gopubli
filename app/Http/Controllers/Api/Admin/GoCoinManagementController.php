<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\GoCoinManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class GoCoinManagementController extends Controller
{
    public function __construct(
        private GoCoinManagementService $gocoinService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['type', 'category', 'wallet_id', 'start_date', 'end_date']);
        $perPage = $request->input('per_page', 10);

        $transactions = $this->gocoinService->getAllTransactions($filters, $perPage);

        return response()->json($transactions);
    }

    public function statistics(): JsonResponse
    {
        $stats = $this->gocoinService->getTransactionStatistics();

        return response()->json(['data' => $stats]);
    }

    public function recentTransactions(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 20);
        $transactions = $this->gocoinService->getRecentTransactions($limit);

        return response()->json(['data' => $transactions]);
    }

    public function walletDetails(int $walletId): JsonResponse
    {
        try {
            $details = $this->gocoinService->getWalletDetails($walletId);

            return response()->json(['data' => $details]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Wallet não encontrada'], 404);
        }
    }

    public function adjustBalance(Request $request, int $walletId): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|integer|not_in:0',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $transaction = $this->gocoinService->adjustWalletBalance(
                $walletId,
                $validated['amount'],
                $validated['reason']
            );

            return response()->json([
                'message' => 'Saldo ajustado com sucesso',
                'data' => $transaction
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
