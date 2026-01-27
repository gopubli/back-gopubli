<?php

namespace App\Services\Admin;

use App\Repositories\Admin\CampaignRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Campaign;

class CampaignManagementService
{
    public function __construct(
        private CampaignRepository $campaignRepository
    ) {}

    public function getAllCampaigns(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->campaignRepository->getAllWithFilters($filters, $perPage);
    }

    public function getCampaignById(int $id): ?Campaign
    {
        return $this->campaignRepository->findWithRelations($id);
    }

    public function updateCampaign(int $id, array $data): bool
    {
        return $this->campaignRepository->update($id, $data);
    }

    public function cancelCampaign(int $id, ?string $reason = null): Campaign
    {
        $campaign = $this->campaignRepository->findOrFail($id);
        
        $updateData = [
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ];

        if ($reason) {
            $updateData['cancellation_reason'] = $reason;
        }

        $campaign->update($updateData);
        
        // Aqui você pode adicionar lógica adicional como:
        // - Notificar a empresa
        // - Reembolsar valores se necessário
        // - Atualizar candidaturas relacionadas
        
        return $campaign->fresh();
    }

    public function deleteCampaign(int $id): bool
    {
        // Soft delete ou verificações antes de deletar
        $campaign = $this->campaignRepository->findOrFail($id);
        
        if (in_array($campaign->status, ['active', 'in_progress'])) {
            throw new \Exception('Não é possível deletar uma campanha ativa ou em andamento.');
        }

        return $this->campaignRepository->delete($id);
    }

    public function getCampaignsByStatus(string $status, int $perPage = 10): LengthAwarePaginator
    {
        return $this->campaignRepository->getByStatus($status, $perPage);
    }

    public function getCampaignStatistics(): array
    {
        $statusCounts = $this->campaignRepository->countByStatus();
        $total = array_sum($statusCounts);
        $completed = $statusCounts['completed'] ?? 0;

        return [
            'total' => $total,
            'by_status' => $statusCounts,
            'active' => $this->campaignRepository->getActiveCampaigns(),
            'success_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            'average_budget' => $this->campaignRepository->getAverageBudget(),
            'total_budget' => $this->campaignRepository->getTotalBudget(),
        ];
    }

    public function getTopCampaigns(int $limit = 10): \Illuminate\Support\Collection
    {
        return $this->campaignRepository->getTopCampaigns($limit);
    }
}
