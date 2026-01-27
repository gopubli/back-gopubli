<?php

namespace App\Repositories\Admin;

use App\Models\CampaignApplication;
use App\Repositories\BaseRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ApplicationRepository extends BaseRepository
{
    public function __construct(CampaignApplication $model)
    {
        parent::__construct($model);
    }

    public function getAllWithFilters(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->with(['campaign.company', 'influencer']);

        $query = $this->applyFilters($query, $filters);

        return $query->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function findWithRelations(int $id): ?CampaignApplication
    {
        return $this->model
            ->with(['campaign.company', 'influencer'])
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

    public function getByCampaign(int $campaignId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->where('campaign_id', $campaignId)
            ->with(['influencer'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getByInfluencer(int $influencerId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->where('influencer_id', $influencerId)
            ->with(['campaign.company'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['search'])) {
            $query->whereHas('influencer', function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%");
            })->orWhereHas('campaign', function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['campaign_id'])) {
            $query->where('campaign_id', $filters['campaign_id']);
        }

        if (!empty($filters['influencer_id'])) {
            $query->where('influencer_id', $filters['influencer_id']);
        }

        return $query;
    }
}
