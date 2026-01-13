<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Influencer;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminManagementController extends Controller
{
    /**
     * Listar todos os influencers
     */
    public function listInfluencers(Request $request)
    {
        try {
            $influencers = Influencer::query()
                ->when($request->search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                })
                ->paginate($request->per_page ?? 15);

            return response()->json($influencers);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar influencers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Visualizar influencer específico
     */
    public function showInfluencer($id)
    {
        try {
            $influencer = Influencer::with(['goCoinWallet', 'campaigns', 'applications'])->findOrFail($id);
            return response()->json($influencer);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Influencer não encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Atualizar influencer
     */
    public function updateInfluencer(Request $request, $id)
    {
        try {
            $influencer = Influencer::findOrFail($id);

            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:influencers,email,' . $id,
                'password' => 'sometimes|string|min:8',
                'cpf' => 'sometimes|nullable|string|unique:influencers,cpf,' . $id,
                'phone' => 'sometimes|nullable|string',
                'instagram' => 'sometimes|nullable|string',
                'tiktok' => 'sometimes|nullable|string',
                'youtube' => 'sometimes|nullable|string',
                'bio' => 'sometimes|nullable|string',
                'followers' => 'sometimes|nullable|integer|min:0',
                'niche' => 'sometimes|nullable|string|max:255',
                'active' => 'sometimes|boolean',
            ]);

            $data = $request->except(['password']);
            
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $influencer->update($data);

            return response()->json([
                'message' => 'Influencer atualizado com sucesso',
                'user' => $influencer->fresh()
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar influencer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deletar influencer
     */
    public function deleteInfluencer($id)
    {
        try {
            $influencer = Influencer::findOrFail($id);
            $influencer->delete();

            return response()->json([
                'message' => 'Influencer deletado com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar influencer',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar todas as empresas
     */
    public function listCompanies(Request $request)
    {
        try {
            $companies = Company::query()
                ->when($request->search, function ($query, $search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                })
                ->with(['subscription', 'goCoinWallet'])
                ->paginate($request->per_page ?? 15);

            return response()->json($companies);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao listar empresas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Visualizar empresa específica
     */
    public function showCompany($id)
    {
        try {
            $company = Company::with(['subscription', 'goCoinWallet', 'campaigns'])->findOrFail($id);
            return response()->json($company);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Empresa não encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Atualizar empresa
     */
    public function updateCompany(Request $request, $id)
    {
        try {
            $company = Company::findOrFail($id);

            $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|string|email|max:255|unique:companies,email,' . $id,
                'password' => 'sometimes|string|min:8',
                'cnpj' => 'sometimes|nullable|string|unique:companies,cnpj,' . $id,
                'phone' => 'sometimes|nullable|string',
                'address' => 'sometimes|nullable|string',
                'active' => 'sometimes|boolean',
            ]);

            $data = $request->except(['password']);
            
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $company->update($data);

            return response()->json([
                'message' => 'Empresa atualizada com sucesso',
                'user' => $company->fresh()
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar empresa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deletar empresa
     */
    public function deleteCompany($id)
    {
        try {
            $company = Company::findOrFail($id);
            $company->delete();

            return response()->json([
                'message' => 'Empresa deletada com sucesso'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao deletar empresa',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
