<?php

namespace App\Repositories\Admin;

use App\Models\Company;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class CompanyRepository extends BaseRepository
{
    public function __construct(Company $model)
    {
        parent::__construct($model);
    }

    public function getAllWithFilters(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->with(['subscription']);

        $query = $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findWithRelations(int $id): ?Company
    {
        return $this->model
            ->with(['subscription', 'campaigns', 'gocoinWallet'])
            ->find($id);
    }

    public function countByStatus(): array
    {
        return $this->model
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public function getActiveCompanies(): int
    {
        return $this->model->where('status', 'active')->count();
    }

    public function getNewCompaniesThisMonth(): int
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
                $q->where('company_name', 'like', "%{$filters['search']}%")
                    ->orWhere('email', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['subscription_status'])) {
            $query->whereHas('subscription', function ($q) use ($filters) {
                $q->where('status', $filters['subscription_status']);
            });
        }

        return $query;
    }
}
