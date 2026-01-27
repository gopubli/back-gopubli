<?php

namespace App\Repositories\Admin;

use App\Models\Influencer;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class InfluencerRepository extends BaseRepository
{
    public function __construct(Influencer $model)
    {
        parent::__construct($model);
    }

    public function getAllWithFilters(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->query();

        $query = $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findWithRelations(int $id): ?Influencer
    {
        return $this->model
            ->with(['applications.campaign', 'gocoinWallet'])
            ->find($id);
    }

    public function countByStatus(): array
    {
        return $this->model
            ->selectRaw('active, COUNT(*) as count')
            ->groupBy('active')
            ->pluck('count', 'active')
            ->toArray();
    }

    public function getActiveInfluencers(): int
    {
        return $this->model->where('active', true)->count();
    }

    public function getNewInfluencersThisMonth(): int
    {
        return $this->model
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['status'])) {
            $active = $filters['status'] === 'active';
            $query->where('active', $active);
        }

        return $query;
    }
}
