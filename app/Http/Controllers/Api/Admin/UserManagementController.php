<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\UserManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserManagementController extends Controller
{
    public function __construct(
        private UserManagementService $userService
    ) {}

    // Influencer Management
    public function listInfluencers(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'status']);
        $perPage = $request->input('per_page', 10);

        $influencers = $this->userService->getAllInfluencers($filters, $perPage);

        return response()->json($influencers);
    }

    public function showInfluencer(int $id): JsonResponse
    {
        $influencer = $this->userService->getInfluencerById($id);

        if (!$influencer) {
            return response()->json(['message' => 'Influenciador não encontrado'], 404);
        }

        return response()->json(['data' => $influencer]);
    }

    public function updateInfluencer(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:influencers,email,' . $id,
            'status' => 'sometimes|in:active,inactive,pending,blocked',
        ]);

        $success = $this->userService->updateInfluencer($id, $validated);

        if (!$success) {
            return response()->json(['message' => 'Erro ao atualizar influenciador'], 500);
        }

        $influencer = $this->userService->getInfluencerById($id);

        return response()->json([
            'message' => 'Influenciador atualizado com sucesso',
            'data' => $influencer
        ]);
    }

    public function deleteInfluencer(int $id): JsonResponse
    {
        $this->userService->deleteInfluencer($id);

        return response()->json(['message' => 'Influenciador deletado com sucesso']);
    }

    public function blockInfluencer(int $id): JsonResponse
    {
        $this->userService->blockInfluencer($id);

        return response()->json(['message' => 'Influenciador bloqueado com sucesso']);
    }

    public function unblockInfluencer(int $id): JsonResponse
    {
        $this->userService->unblockInfluencer($id);

        return response()->json(['message' => 'Influenciador desbloqueado com sucesso']);
    }

    // Company Management
    public function listCompanies(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'status', 'subscription_status']);
        $perPage = $request->input('per_page', 10);

        $companies = $this->userService->getAllCompanies($filters, $perPage);

        return response()->json($companies);
    }

    public function showCompany(int $id): JsonResponse
    {
        $company = $this->userService->getCompanyById($id);

        if (!$company) {
            return response()->json(['message' => 'Empresa não encontrada'], 404);
        }

        return response()->json(['data' => $company]);
    }

    public function updateCompany(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'company_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:companies,email,' . $id,
            'status' => 'sometimes|in:active,inactive,pending,blocked',
        ]);

        $success = $this->userService->updateCompany($id, $validated);

        if (!$success) {
            return response()->json(['message' => 'Erro ao atualizar empresa'], 500);
        }

        $company = $this->userService->getCompanyById($id);

        return response()->json([
            'message' => 'Empresa atualizada com sucesso',
            'data' => $company
        ]);
    }

    public function deleteCompany(int $id): JsonResponse
    {
        $this->userService->deleteCompany($id);

        return response()->json(['message' => 'Empresa deletada com sucesso']);
    }

    public function blockCompany(int $id): JsonResponse
    {
        $this->userService->blockCompany($id);

        return response()->json(['message' => 'Empresa bloqueada com sucesso']);
    }

    public function unblockCompany(int $id): JsonResponse
    {
        $this->userService->unblockCompany($id);

        return response()->json(['message' => 'Empresa desbloqueada com sucesso']);
    }

    // Statistics
    public function userStatistics(): JsonResponse
    {
        $stats = $this->userService->getUserStatistics();

        return response()->json(['data' => $stats]);
    }
}
