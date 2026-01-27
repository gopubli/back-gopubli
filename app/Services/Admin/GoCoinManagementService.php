<?php

namespace App\Services\Admin;

use App\Repositories\Admin\GoCoinRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\GoCoinWallet;
use App\Models\GoCoinTransaction;
use Illuminate\Support\Facades\DB;

class GoCoinManagementService
{
    public function __construct(
        private GoCoinRepository $gocoinRepository
    ) {}

    public function getAllTransactions(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->gocoinRepository->getAllWithFilters($filters, $perPage);
    }

    public function getTransactionStatistics(): array
    {
        return [
            'total_transactions' => $this->gocoinRepository->getTotalTransactions(),
            'total_coins_in_circulation' => $this->gocoinRepository->getTotalCoinsInCirculation(),
            'total_coins_earned' => $this->gocoinRepository->getTotalCoinsEarned(),
            'total_coins_redeemed' => $this->gocoinRepository->getTotalCoinsRedeemed(),
            'by_type' => $this->gocoinRepository->getTransactionsByType(),
            'by_category' => $this->gocoinRepository->getTransactionsByCategory(),
        ];
    }

    public function getRecentTransactions(int $limit = 20): \Illuminate\Support\Collection
    {
        return $this->gocoinRepository->getRecentTransactions($limit);
    }

    public function adjustWalletBalance(int $walletId, int $amount, string $reason): GoCoinTransaction
    {
        return DB::transaction(function () use ($walletId, $amount, $reason) {
            $wallet = GoCoinWallet::findOrFail($walletId);
            
            $transaction = GoCoinTransaction::create([
                'wallet_id' => $walletId,
                'amount' => abs($amount),
                'type' => $amount > 0 ? 'earn' : 'redeem',
                'category' => 'admin_adjustment',
                'description' => $reason,
                'balance_after' => $wallet->balance + $amount,
            ]);

            $wallet->increment('balance', $amount);

            return $transaction;
        });
    }

    public function getWalletDetails(int $walletId): array
    {
        $wallet = GoCoinWallet::with(['walletable'])->findOrFail($walletId);
        
        $transactions = GoCoinTransaction::where('wallet_id', $walletId)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return [
            'wallet' => $wallet,
            'transactions' => $transactions,
            'statistics' => [
                'total_earned' => $transactions->where('type', 'earn')->sum('amount'),
                'total_redeemed' => $transactions->where('type', 'redeem')->sum('amount'),
                'transaction_count' => $transactions->count(),
            ],
        ];
    }
}
