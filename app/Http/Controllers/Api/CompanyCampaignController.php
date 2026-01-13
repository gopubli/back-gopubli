<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyCampaignController extends Controller
{
    /**
     * Listar campanhas da empresa
     */
    public function index(Request $request)
    {
        $company = $request->user();

        $campaigns = Campaign::where('company_id', $company->id)
            ->with(['selectedInfluencer', 'applications'])
            ->withCount('applications')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($campaigns);
    }

    /**
     * Criar nova campanha
     */
    public function store(Request $request)
    {
        $company = $request->user();

        // Verificar se a empresa aceitou os termos
        if (!$company->hasAcceptedTerm('confidentiality')) {
            return response()->json([
                'message' => 'Você precisa aceitar o Termo de Confidencialidade antes de criar campanhas',
            ], 403);
        }

        // Verificar assinatura ativa
        if (!$company->hasActiveSubscription()) {
            return response()->json([
                'message' => 'Sua assinatura não está ativa. Por favor, regularize seu pagamento.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'objective' => 'nullable|in:branding,traffic,conversion',
            'category' => 'nullable|string|max:100',
            'platform' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'requirements' => 'nullable|string',
            'deliverables' => 'nullable|string',
            'total_value' => 'required|numeric|min:200', // Aceita total_value
            'amount' => 'nullable|numeric|min:200', // Ou amount
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Aceitar tanto 'amount' quanto 'total_value'
        $amount = $request->input('amount') ?? $request->input('total_value');

        // Criar campanha
        $campaign = Campaign::create([
            'company_id' => $company->id,
            'title' => $request->title,
            'description' => $request->description,
            'objective' => $request->objective ?? 'branding',
            'category' => $request->category,
            'platform' => $request->platform,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'requirements' => $request->requirements,
            'deliverables' => $request->deliverables,
            'amount' => $amount,
            'min_amount' => 200.00,
            'status' => 'draft',
            'payment_status' => 'pending',
        ]);

        // Calcular distribuição (60/20/20)
        $campaign->calculateDistribution();

        return response()->json([
            'message' => 'Campanha criada com sucesso. Proceda com o pagamento para ativá-la.',
            'campaign' => $campaign,
        ], 201);
    }

    /**
     * Exibir campanha específica
     */
    public function show(Request $request, $id)
    {
        $company = $request->user();

        $campaign = Campaign::where('company_id', $company->id)
            ->with(['selectedInfluencer', 'applications.influencer'])
            ->findOrFail($id);

        return response()->json($campaign);
    }

    /**
     * Atualizar campanha
     */
    public function update(Request $request, $id)
    {
        $company = $request->user();

        $campaign = Campaign::where('company_id', $company->id)->findOrFail($id);

        // Não permitir edição se já foi paga ou está em andamento
        if (in_array($campaign->status, ['in_progress', 'completed'])) {
            return response()->json([
                'message' => 'Não é possível editar uma campanha em andamento ou concluída',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'objective' => 'sometimes|in:branding,traffic,conversion',
            'amount' => 'sometimes|numeric|min:200',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors(),
            ], 422);
        }

        $campaign->update($request->only(['title', 'description', 'objective', 'amount']));

        // Recalcular distribuição se o valor mudou
        if ($request->has('amount')) {
            $campaign->calculateDistribution();
        }

        return response()->json([
            'message' => 'Campanha atualizada com sucesso',
            'campaign' => $campaign->fresh(),
        ]);
    }

    /**
     * Confirmar pagamento da campanha (simular integração)
     */
    public function confirmPayment(Request $request, $id)
    {
        $company = $request->user();

        $campaign = Campaign::where('company_id', $company->id)->findOrFail($id);

        if ($campaign->payment_status === 'paid') {
            return response()->json([
                'message' => 'Esta campanha já foi paga',
            ], 400);
        }

        // Aqui seria a integração com Asaas ou outro gateway
        // Por enquanto, simular pagamento confirmado

        $campaign->update([
            'payment_status' => 'paid',
            'payment_confirmed_at' => now(),
            'status' => 'open', // Campanha fica disponível para influencers
        ]);

        return response()->json([
            'message' => 'Pagamento confirmado! Sua campanha está disponível para influencers.',
            'campaign' => $campaign,
        ]);
    }

    /**
     * Listar candidaturas da campanha com dados completos dos influencers
     */
    public function applications(Request $request, $id)
    {
        $company = $request->user();
        $campaign = Campaign::where('company_id', $company->id)->findOrFail($id);
        $applications = CampaignApplication::where('campaign_id', $campaign->id)
            ->with(['influencer' => function ($query) {
                $query->select(
                    'id',
                    'name',
                    'email',
                    'cpf',
                    'phone',
                    'instagram',
                    'tiktok',
                    'youtube',
                    'avatar',
                    'bio',
                    'followers',
                    'niche',
                    'active',
                    'email_verified_at'
                );
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        // Adicionar informações úteis
        $applications->transform(function ($application) {
            $application->influencer_stats = [
                'total_campaigns' => $application->influencer->selectedCampaigns()->count(),
                'completed_campaigns' => $application->influencer->selectedCampaigns()
                    ->where('status', 'completed')
                    ->count(),
            ];
            return $application;
        });

        return response()->json([
            'campaign' => [
                'id' => $campaign->id,
                'title' => $campaign->title,
                'description' => $campaign->description,
                'amount' => $campaign->amount,
                'status' => $campaign->status,
            ],
            'total_applications' => $applications->count(),
            'applications' => $applications,
        ]);
    }

    /**
     * Aceitar candidatura de um influencer
     */
    public function acceptApplication(Request $request, $campaignId, $applicationId)
    {
        $company = $request->user();

        $campaign = Campaign::where('company_id', $company->id)->findOrFail($campaignId);

        if ($campaign->selected_influencer_id) {
            return response()->json([
                'message' => 'Esta campanha já tem um influencer selecionado',
            ], 400);
        }

        $application = CampaignApplication::where('campaign_id', $campaign->id)
            ->findOrFail($applicationId);

        // Aceitar aplicação
        $application->accept();

        // Agora a empresa pode ver os dados completos do influencer
        $influencer = $application->influencer;

        return response()->json([
            'message' => 'Influencer selecionado com sucesso!',
            'campaign' => $campaign->fresh(),
            'influencer' => $influencer->getFullData(),
        ]);
    }

    /**
     * Rejeitar candidatura
     */
    public function rejectApplication(Request $request, $campaignId, $applicationId)
    {
        $company = $request->user();

        $campaign = Campaign::where('company_id', $company->id)->findOrFail($campaignId);

        $application = CampaignApplication::where('campaign_id', $campaign->id)
            ->findOrFail($applicationId);

        $validator = Validator::make($request->all(), [
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors(),
            ], 422);
        }

        $application->reject($request->reason);

        return response()->json([
            'message' => 'Candidatura rejeitada',
        ]);
    }

    /**
     * Finalizar campanha
     */
    public function complete(Request $request, $id)
    {
        $company = $request->user();

        $campaign = Campaign::where('company_id', $company->id)->findOrFail($id);

        if ($campaign->status !== 'in_progress') {
            return response()->json([
                'message' => 'Apenas campanhas em andamento podem ser finalizadas',
            ], 400);
        }

        $campaign->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        // Liberar pagamento para o influencer via GO Coin
        $influencer = $campaign->selectedInfluencer;
        if ($influencer) {
            $wallet = $influencer->getOrCreateWallet();
            $wallet->addCredit(
                $campaign->influencer_amount,
                'campaign_payment',
                "Pagamento da campanha: {$campaign->title}",
                $campaign
            );

            // Bonificação adicional (exemplo: 5% bônus)
            $bonus = $campaign->influencer_amount * 0.05;
            $wallet->addCredit(
                $bonus,
                'campaign_bonus',
                "Bônus de performance da campanha: {$campaign->title}",
                $campaign
            );
        }

        return response()->json([
            'message' => 'Campanha finalizada com sucesso!',
            'campaign' => $campaign->fresh(),
        ]);
    }

    /**
     * Cancelar campanha
     */
    public function cancel(Request $request, $id)
    {
        $company = $request->user();

        $campaign = Campaign::where('company_id', $company->id)->findOrFail($id);

        if (in_array($campaign->status, ['completed', 'cancelled'])) {
            return response()->json([
                'message' => 'Não é possível cancelar uma campanha já concluída ou cancelada',
            ], 400);
        }

        $campaign->update([
            'status' => 'cancelled',
        ]);

        // Se houve pagamento, processar reembolso
        if ($campaign->payment_status === 'paid') {
            $campaign->update([
                'payment_status' => 'refunded',
            ]);

            // Aqui seria a lógica de reembolso
        }

        return response()->json([
            'message' => 'Campanha cancelada',
            'campaign' => $campaign->fresh(),
        ]);
    }

    /**
     * Dashboard com estatísticas
     */
    public function dashboard(Request $request)
    {
        $company = $request->user();

        $stats = [
            'total_campaigns' => Campaign::where('company_id', $company->id)->count(),
            'active_campaigns' => Campaign::where('company_id', $company->id)->active()->count(),
            'completed_campaigns' => Campaign::where('company_id', $company->id)->where('status', 'completed')->count(),
            'total_spent' => Campaign::where('company_id', $company->id)
                ->where('payment_status', 'paid')
                ->sum('amount'),
            'pending_applications' => CampaignApplication::whereHas('campaign', function ($q) use ($company) {
                $q->where('company_id', $company->id);
            })->pending()->count(),
        ];

        return response()->json($stats);
    }
}
