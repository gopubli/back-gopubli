<?php

namespace App\Services\Admin;

use App\Repositories\Admin\InfluencerRepository;
use App\Repositories\Admin\CompanyRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class UserManagementService
{
    public function __construct(
        private InfluencerRepository $influencerRepository,
        private CompanyRepository $companyRepository
    ) {}

    // Influencer Methods
    public function getAllInfluencers(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->influencerRepository->getAllWithFilters($filters, $perPage);
    }

    public function getInfluencerById(int $id)
    {
        return $this->influencerRepository->findWithRelations($id);
    }

    public function updateInfluencer(int $id, array $data): bool
    {
        return $this->influencerRepository->update($id, $data);
    }

    public function deleteInfluencer(int $id): bool
    {
        return $this->influencerRepository->delete($id);
    }

    public function blockInfluencer(int $id): bool
    {
        return $this->influencerRepository->update($id, ['status' => 'blocked']);
    }

    public function unblockInfluencer(int $id): bool
    {
        return $this->influencerRepository->update($id, ['status' => 'active']);
    }

    // Company Methods
    public function getAllCompanies(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->companyRepository->getAllWithFilters($filters, $perPage);
    }

    public function getCompanyById(int $id)
    {
        return $this->companyRepository->findWithRelations($id);
    }

    public function updateCompany(int $id, array $data): bool
    {
        return $this->companyRepository->update($id, $data);
    }

    public function deleteCompany(int $id): bool
    {
        return $this->companyRepository->delete($id);
    }

    public function blockCompany(int $id): bool
    {
        return $this->companyRepository->update($id, ['status' => 'blocked']);
    }

    public function unblockCompany(int $id): bool
    {
        return $this->companyRepository->update($id, ['status' => 'active']);
    }

    // Statistics
    public function getUserStatistics(): array
    {
        $influencerStats = $this->influencerRepository->countByStatus();
        $companyStats = $this->companyRepository->countByStatus();

        return [
            'influencers' => [
                'total' => $this->influencerRepository->count(),
                'active' => $this->influencerRepository->getActiveInfluencers(),
                'new_this_month' => $this->influencerRepository->getNewInfluencersThisMonth(),
                'by_status' => $influencerStats,
            ],
            'companies' => [
                'total' => $this->companyRepository->count(),
                'active' => $this->companyRepository->getActiveCompanies(),
                'new_this_month' => $this->companyRepository->getNewCompaniesThisMonth(),
                'by_status' => $companyStats,
            ],
            'total_users' => $this->influencerRepository->count() + $this->companyRepository->count(),
        ];
    }
}
