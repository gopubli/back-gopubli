<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function dashboard(): JsonResponse
    {
        $stats = $this->reportService->getDashboardStatistics();

        return response()->json(['data' => $stats]);
    }

    public function revenue(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $dateRange = $validated ? [
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ] : null;

        $report = $this->reportService->getRevenueReport($dateRange);

        return response()->json(['data' => $report]);
    }

    public function campaigns(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $dateRange = $validated ? [
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
        ] : null;

        $report = $this->reportService->getCampaignReport($dateRange);

        return response()->json(['data' => $report]);
    }

    public function userGrowth(Request $request): JsonResponse
    {
        $period = $request->input('period', 'month');

        if (!in_array($period, ['week', 'month', 'year'])) {
            return response()->json(['message' => 'Período inválido'], 400);
        }

        $report = $this->reportService->getUserGrowthReport($period);

        return response()->json(['data' => $report]);
    }

    // Export methods podem ser adicionados aqui
    // public function exportRevenue(Request $request) { ... }
    // public function exportCampaigns(Request $request) { ... }
}
