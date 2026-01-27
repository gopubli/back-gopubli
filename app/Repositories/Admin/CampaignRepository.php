<?php

namespace App\Repositories\Admin;

use App\Models\Campaign;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class CampaignRepository extends BaseRepository
{
    public function __construct(Campaign $model)
    {
        parent::__construct($model);
    }

    public function getAllWithFilters(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->with(['company']);

        $query = $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findWithRelations(int $id): ?Campaign
    {
        return $this->model
            ->with(['company', 'applications.influencer'])
            ->find($id);
    }

    public function getByStatus(string $status, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->where('status', $status)
            ->with(['company'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function countByStatus(): array
    {
        return $this->model
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function getTotalBudget(): float
    {
        return $this->model->sum('budget') ?? 0;
    }

    public function getAverageBudget(): float
    {
        return $this->model->avg('budget') ?? 0;
    }

    public function getActiveCampaigns(): int
    {
        return $this->model
            ->whereIn('status', ['active', 'in_progress'])
            ->count();
    }

    public function getTopCampaigns(int $limit = 10): \Illuminate\Support\Collection
    {
        return $this->model
            ->with(['company'])
            ->withCount('applications')
            ->orderBy('applications_count', 'desc')
            ->limit($limit)
            ->get();
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (!empty($filters['start_date'])) {
            $query->whereDate('start_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->whereDate('end_date', '<=', $filters['end_date']);
        }

        return $query;
    }
}
