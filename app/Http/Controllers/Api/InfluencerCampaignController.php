<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InfluencerCampaignController extends Controller
{
    /**
     * Listar campanhas disponíveis
     */
    public function available(Request $request)
    {
        $influencer = $request->user();

        // Apenas campanhas pagas, abertas e não bloqueadas
        $campaigns = Campaign::available()
            ->with([
                'company:id,name,logo,cnpj',
                'applications:id,campaign_id,influencer_id,status'
            ])
            ->withCount('applications')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Adicionar informações úteis
        $campaigns->getCollection()->transform(function ($campaign) use ($influencer) {
            // Verificar se o influencer já se candidatou
            $hasApplied = CampaignApplication::where('campaign_id', $campaign->id)
                ->where('influencer_id', $influencer->id)
                ->exists();

            $campaign->has_applied = $hasApplied;
            
            // Ocultar dados sensíveis da empresa até ser selecionado
            $campaign->company->makeHidden(['email', 'phone', 'address', 'cnpj']);

            return $campaign;
        });

        return response()->json($campaigns);
    }

    /**
     * Listar minhas candidaturas com status de match
     */
    public function myApplications(Request $request)
    {
        $influencer = $request->user();

        $applications = CampaignApplication::where('influencer_id', $influencer->id)
            ->with(['campaign.company:id,name,logo,email,phone,cnpj'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Adicionar informações de match
        $applications->getCollection()->transform(function ($application) {
            $application->has_match = $application->status === 'accepted';
            $application->status_label = [
                'pending' => 'Aguardando resposta',
                'accepted' => '✅ MATCH! Você foi selecionado',
                'rejected' => 'Não selecionado',
                'withdrawn' => 'Cancelada'
            ][$application->status] ?? $application->status;

            // Se foi aceito, mostrar dados completos da empresa
            if ($application->status === 'accepted') {
                // Dados completos já vêm do with acima
            } else {
                // Ocultar dados sensíveis da empresa
                $application->campaign->company->makeHidden(['email', 'phone', 'cnpj']);
            }

            return $application;
        });

        return response()->json([
            'total' => $applications->total(),
            'pending_count' => CampaignApplication::where('influencer_id', $influencer->id)
                ->where('status', 'pending')->count(),
            'accepted_count' => CampaignApplication::where('influencer_id', $influencer->id)
                ->where('status', 'accepted')->count(),
            'rejected_count' => CampaignApplication::where('influencer_id', $influencer->id)
                ->where('status', 'rejected')->count(),
            'applications' => $applications,
        ]);
    }

    /**
     * Listar campanhas em que fui selecionado
     */
    public function myCampaigns(Request $request)
    {
        $influencer = $request->user();

        $campaigns = Campaign::where('selected_influencer_id', $influencer->id)
            ->with('company')
            ->orderBy('started_at', 'desc')
            ->paginate(15);

        // Após ser selecionado, pode ver dados completos da empresa
        $campaigns->getCollection()->transform(function ($campaign) {
            return $campaign;
        });

        return response()->json($campaigns);
    }

    /**
     * Ver detalhes de uma campanha disponível
     */
    public function show(Request $request, $id)
    {
        $influencer = $request->user();

        $campaign = Campaign::available()->findOrFail($id);

        // Verificar se já se candidatou
        $application = CampaignApplication::where('campaign_id', $campaign->id)
            ->where('influencer_id', $influencer->id)
            ->first();

        $campaign->has_applied = (bool) $application;
        $campaign->my_application = $application;

        // Ocultar dados sensíveis da empresa
        $campaign->company->makeHidden(['email', 'phone', 'address', 'cnpj']);

        return response()->json($campaign);
    }

    /**
     * Candidatar-se a uma campanha
     */
    public function apply(Request $request, $id)
    {
        $influencer = $request->user();

        // Verificar se aceitou os termos
        if (!$influencer->hasAcceptedTerm('confidentiality')) {
            return response()->json([
                'message' => 'Você precisa aceitar o Termo de Confidencialidade antes de se candidatar a campanhas',
            ], 403);
        }

        $campaign = Campaign::available()->findOrFail($id);

        // Verificar se já se candidatou
        $existingApplication = CampaignApplication::where('campaign_id', $campaign->id)
            ->where('influencer_id', $influencer->id)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'message' => 'Você já se candidatou a esta campanha',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'offered_amount' => 'nullable|numeric|min:0',
            'proposal_message' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Criar candidatura
        $application = CampaignApplication::create([
            'campaign_id' => $campaign->id,
            'influencer_id' => $influencer->id,
            'offered_amount' => $request->offered_amount,
            'proposal_message' => $request->proposal_message,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Candidatura enviada com sucesso!',
            'application' => $application,
        ], 201);
    }

    /**
     * Retirar candidatura
     */
    public function withdrawApplication(Request $request, $id)
    {
        $influencer = $request->user();

        $application = CampaignApplication::where('influencer_id', $influencer->id)
            ->findOrFail($id);

        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Não é possível retirar uma candidatura que já foi processada',
            ], 400);
        }

        $application->withdraw();

        return response()->json([
            'message' => 'Candidatura retirada com sucesso',
        ]);
    }

    /**
     * Atualizar proposta de candidatura
     */
    public function updateApplication(Request $request, $id)
    {
        $influencer = $request->user();

        $application = CampaignApplication::where('influencer_id', $influencer->id)
            ->findOrFail($id);

        if ($application->status !== 'pending') {
            return response()->json([
                'message' => 'Não é possível editar uma candidatura que já foi processada',
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'offered_amount' => 'sometimes|numeric|min:0',
            'proposal_message' => 'sometimes|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors(),
            ], 422);
        }

        $application->update($request->only(['offered_amount', 'proposal_message']));

        return response()->json([
            'message' => 'Proposta atualizada com sucesso',
            'application' => $application->fresh(),
        ]);
    }

    /**
     * Dashboard com estatísticas
     */
    public function dashboard(Request $request)
    {
        $influencer = $request->user();

        $stats = [
            'total_applications' => CampaignApplication::where('influencer_id', $influencer->id)->count(),
            'pending_applications' => CampaignApplication::where('influencer_id', $influencer->id)
                ->pending()
                ->count(),
            'accepted_applications' => CampaignApplication::where('influencer_id', $influencer->id)
                ->accepted()
                ->count(),
            'active_campaigns' => Campaign::where('selected_influencer_id', $influencer->id)
                ->active()
                ->count(),
            'completed_campaigns' => Campaign::where('selected_influencer_id', $influencer->id)
                ->where('status', 'completed')
                ->count(),
            'total_earned' => $influencer->goCoinWallet?->total_earned ?? 0,
            'current_balance' => $influencer->goCoinWallet?->balance ?? 0,
        ];

        return response()->json($stats);
    }

    /**
     * Ver campanha em que foi selecionado (com dados completos)
     */
    public function showMyCampaign(Request $request, $id)
    {
        $influencer = $request->user();

        $campaign = Campaign::where('selected_influencer_id', $influencer->id)
            ->with('company')
            ->findOrFail($id);

        // Agora pode ver todos os dados da empresa
        return response()->json([
            'campaign' => $campaign,
            'company_full_data' => [
                'id' => $campaign->company->id,
                'name' => $campaign->company->name,
                'email' => $campaign->company->email,
                'phone' => $campaign->company->phone,
                'cnpj' => $campaign->company->cnpj,
                'address' => $campaign->company->address,
                'logo' => $campaign->company->logo,
            ],
        ]);
    }
}
