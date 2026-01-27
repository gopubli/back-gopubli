<?php

namespace App\Repositories\Admin;

use App\Models\GoCoinTransaction;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class GoCoinRepository extends BaseRepository
{
    public function __construct(GoCoinTransaction $model)
    {
        parent::__construct($model);
    }

    public function getAllWithFilters(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->with(['wallet']);

        $query = $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getTotalTransactions(): int
    {
        return $this->model->count();
    }

    public function getTotalCoinsInCirculation(): int
    {
        return DB::table('gocoin_wallets')->sum('balance') ?? 0;
    }

    public function getTotalCoinsEarned(): int
    {
        return $this->model
            ->where('type', 'earn')
            ->sum('amount') ?? 0;
    }

    public function getTotalCoinsRedeemed(): int
    {
        return $this->model
            ->where('type', 'redeem')
            ->sum('amount') ?? 0;
    }

    public function getTransactionsByType(): array
    {
        return $this->model
            ->selectRaw('type, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->type => [
                    'count' => $item->count,
                    'total_amount' => $item->total_amount
                ]];
            })
            ->toArray();
    }

    public function getTransactionsByCategory(): array
    {
        return $this->model
            ->selectRaw('category, COUNT(*) as count, SUM(amount) as total_amount')
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category => [
                    'count' => $item->count,
                    'total_amount' => $item->total_amount
                ]];
            })
            ->toArray();
    }

    public function getRecentTransactions(int $limit = 20): \Illuminate\Support\Collection
    {
        return $this->model
            ->with(['wallet'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['wallet_id'])) {
            $query->where('wallet_id', $filters['wallet_id']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('created_at', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('created_at', '<=', $filters['end_date']);
        }

        return $query;
    }
}
