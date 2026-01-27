<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\ApplicationManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApplicationManagementController extends Controller
{
    public function __construct(
        private ApplicationManagementService $applicationService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'status', 'campaign_id', 'influencer_id']);
        $perPage = $request->input('per_page', 10);

        $applications = $this->applicationService->getAllApplications($filters, $perPage);

        return response()->json($applications);
    }

    public function show(int $id): JsonResponse
    {
        $application = $this->applicationService->getApplicationById($id);

        if (!$application) {
            return response()->json(['message' => 'Candidatura não encontrada'], 404);
        }

        return response()->json(['data' => $application]);
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,rejected,withdrawn',
            'reason' => 'nullable|string|max:500',
        ]);

        $application = $this->applicationService->updateApplicationStatus(
            $id,
            $validated['status'],
            $validated['reason'] ?? null
        );

        return response()->json([
            'message' => 'Status da candidatura atualizado com sucesso',
            'data' => $application
        ]);
    }

    public function statistics(): JsonResponse
    {
        $stats = $this->applicationService->getApplicationStatistics();

        return response()->json(['data' => $stats]);
    }
}
