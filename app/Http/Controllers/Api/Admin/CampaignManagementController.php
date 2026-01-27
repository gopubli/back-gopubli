<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\CampaignManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CampaignManagementController extends Controller
{
    public function __construct(
        private CampaignManagementService $campaignService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'status', 'company_id', 'start_date', 'end_date']);
        $perPage = $request->input('per_page', 10);

        $campaigns = $this->campaignService->getAllCampaigns($filters, $perPage);

        return response()->json($campaigns);
    }

    public function show(int $id): JsonResponse
    {
        $campaign = $this->campaignService->getCampaignById($id);

        if (!$campaign) {
            return response()->json(['message' => 'Campanha não encontrada'], 404);
        }

        return response()->json(['data' => $campaign]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'status' => 'sometimes|in:draft,pending_payment,active,in_progress,completed,cancelled',
            'budget' => 'sometimes|numeric|min:0',
        ]);

        $success = $this->campaignService->updateCampaign($id, $validated);

        if (!$success) {
            return response()->json(['message' => 'Erro ao atualizar campanha'], 500);
        }

        $campaign = $this->campaignService->getCampaignById($id);

        return response()->json([
            'message' => 'Campanha atualizada com sucesso',
            'data' => $campaign
        ]);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            $campaign = $this->campaignService->cancelCampaign($id, $validated['reason'] ?? null);

            return response()->json([
                'message' => 'Campanha cancelada com sucesso',
                'data' => $campaign
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->campaignService->deleteCampaign($id);

            return response()->json(['message' => 'Campanha deletada com sucesso']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function statistics(): JsonResponse
    {
        $stats = $this->campaignService->getCampaignStatistics();

        return response()->json(['data' => $stats]);
    }

    public function topCampaigns(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $campaigns = $this->campaignService->getTopCampaigns($limit);

        return response()->json(['data' => $campaigns]);
    }
}
