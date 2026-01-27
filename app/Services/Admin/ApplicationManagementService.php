<?php

namespace App\Services\Admin;

use App\Repositories\Admin\ApplicationRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\CampaignApplication;

class ApplicationManagementService
{
    public function __construct(
        private ApplicationRepository $applicationRepository
    ) {}

    public function getAllApplications(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->applicationRepository->getAllWithFilters($filters, $perPage);
    }

    public function getApplicationById(int $id): ?CampaignApplication
    {
        return $this->applicationRepository->findWithRelations($id);
    }

    public function updateApplicationStatus(int $id, string $status, ?string $reason = null): CampaignApplication
    {
        $application = $this->applicationRepository->findOrFail($id);
        
        $updateData = [
            'status' => $status,
            'updated_at' => now(),
        ];

        if ($reason) {
            $updateData['admin_notes'] = $reason;
        }

        $application->update($updateData);
        
        // Adicionar lógica adicional como:
        // - Notificar influencer
        // - Notificar empresa
        // - Atualizar métricas
        
        return $application->fresh();
    }

    public function getApplicationsByCampaign(int $campaignId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->applicationRepository->getByCampaign($campaignId, $perPage);
    }

    public function getApplicationsByInfluencer(int $influencerId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->applicationRepository->getByInfluencer($influencerId, $perPage);
    }

    public function getApplicationStatistics(): array
    {
        $statusCounts = $this->applicationRepository->countByStatus();
        $total = array_sum($statusCounts);

        return [
            'total' => $total,
            'by_status' => $statusCounts,
            'pending' => $statusCounts['pending'] ?? 0,
            'accepted' => $statusCounts['accepted'] ?? 0,
            'rejected' => $statusCounts['rejected'] ?? 0,
            'acceptance_rate' => $total > 0 ? round((($statusCounts['accepted'] ?? 0) / $total) * 100, 2) : 0,
        ];
    }
}
