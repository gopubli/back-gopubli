<?php

namespace App\Services\Admin;

use App\Repositories\Admin\CampaignRepository;
use App\Repositories\Admin\CompanyRepository;
use App\Repositories\Admin\InfluencerRepository;
use App\Repositories\Admin\ApplicationRepository;
use App\Repositories\Admin\GoCoinRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    public function __construct(
        private CampaignRepository $campaignRepository,
        private CompanyRepository $companyRepository,
        private InfluencerRepository $influencerRepository,
        private ApplicationRepository $applicationRepository,
        private GoCoinRepository $gocoinRepository
    ) {}

    public function getDashboardStatistics(): array
    {
        return [
            'total_companies' => $this->companyRepository->count(),
            'active_companies' => $this->companyRepository->getActiveCompanies(),
            'total_influencers' => $this->influencerRepository->count(),
            'active_influencers' => $this->influencerRepository->getActiveInfluencers(),
            'total_campaigns' => $this->campaignRepository->count(),
            'active_campaigns' => $this->campaignRepository->getActiveCampaigns(),
            'total_applications' => $this->applicationRepository->count(),
            'total_revenue' => $this->calculateTotalRevenue(),
            'platform_fee_revenue' => $this->calculatePlatformFeeRevenue(),
            'monthly_revenue' => $this->calculateMonthlyRevenue(),
            'growth_rate' => $this->calculateGrowthRate(),
        ];
    }

    public function getRevenueReport(?array $dateRange = null): array
    {
        $startDate = $dateRange['start_date'] ?? now()->subMonths(12);
        $endDate = $dateRange['end_date'] ?? now();

        // Aqui você deve adaptar conforme seu modelo de receita
        // Este é um exemplo básico
        $campaigns = DB::table('campaigns')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period')
            ->selectRaw('COUNT(*) as campaigns_count')
            ->selectRaw('SUM(budget) as total_budget')
            ->selectRaw('SUM(budget * 0.10) as platform_fees') // 10% de taxa
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $subscriptions = DB::table('subscriptions')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'active')
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period')
            ->selectRaw('COUNT(*) as count')
            ->selectRaw('SUM(amount) as total')
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // Combinar dados
        $report = [];
        foreach ($campaigns as $campaign) {
            $subscription = $subscriptions->firstWhere('period', $campaign->period);
            
            $report[] = [
                'period' => $campaign->period,
                'total_revenue' => ($campaign->platform_fees ?? 0) + ($subscription->total ?? 0),
                'platform_fees' => $campaign->platform_fees ?? 0,
                'subscription_revenue' => $subscription->total ?? 0,
                'campaign_revenue' => $campaign->total_budget ?? 0,
                'transactions_count' => $campaign->campaigns_count + ($subscription->count ?? 0),
            ];
        }

        return $report;
    }

    public function getCampaignReport(?array $dateRange = null): array
    {
        $startDate = $dateRange['start_date'] ?? now()->subMonths(12);
        $endDate = $dateRange['end_date'] ?? now();

        $statusCounts = DB::table('campaigns')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $total = array_sum($statusCounts);
        $completed = $statusCounts['completed'] ?? 0;

        return [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'total' => $total,
            'by_status' => $statusCounts,
            'success_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            'average_budget' => $this->campaignRepository->getAverageBudget(),
            'total_budget' => $this->campaignRepository->getTotalBudget(),
            'top_campaigns' => $this->campaignRepository->getTopCampaigns(10),
        ];
    }

    public function getUserGrowthReport(string $period = 'month'): array
    {
        $dateFormat = match($period) {
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m',
        };

        $influencers = DB::table('influencers')
            ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period")
            ->selectRaw('COUNT(*) as count')
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $companies = DB::table('companies')
            ->selectRaw("DATE_FORMAT(created_at, '{$dateFormat}') as period")
            ->selectRaw('COUNT(*) as count')
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return [
            'influencers' => $influencers,
            'companies' => $companies,
            'period_type' => $period,
        ];
    }

    private function calculateTotalRevenue(): float
    {
        $platformFees = DB::table('campaigns')
            ->where('status', '!=', 'cancelled')
            ->sum(DB::raw('budget * 0.10')) ?? 0;

        $subscriptions = DB::table('subscriptions')
            ->where('status', 'active')
            ->sum('amount') ?? 0;

        return $platformFees + $subscriptions;
    }

    private function calculatePlatformFeeRevenue(): float
    {
        return DB::table('campaigns')
            ->where('status', '!=', 'cancelled')
            ->sum(DB::raw('budget * 0.10')) ?? 0;
    }

    private function calculateMonthlyRevenue(): float
    {
        $platformFees = DB::table('campaigns')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', '!=', 'cancelled')
            ->sum(DB::raw('budget * 0.10')) ?? 0;

        $subscriptions = DB::table('subscriptions')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'active')
            ->sum('amount') ?? 0;

        return $platformFees + $subscriptions;
    }

    private function calculateGrowthRate(): float
    {
        $currentMonth = $this->calculateMonthlyRevenue();
        
        $lastMonthRevenue = DB::table('campaigns')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->where('status', '!=', 'cancelled')
            ->sum(DB::raw('budget * 0.10')) ?? 0;

        if ($lastMonthRevenue == 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return round((($currentMonth - $lastMonthRevenue) / $lastMonthRevenue) * 100, 2);
    }
}
