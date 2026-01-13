<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdministratorAuthController;
use App\Http\Controllers\Api\CompanyAuthController;
use App\Http\Controllers\Api\InfluencerAuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PasswordResetController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\CompanyCampaignController;
use App\Http\Controllers\Api\InfluencerCampaignController;
use App\Http\Controllers\Api\GoCoinController;
use App\Http\Controllers\Api\TermsController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\AdminManagementController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rotas públicas de autenticação - Administrador
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdministratorAuthController::class, 'login']);
    // REGISTRO DE ADMINISTRADOR REMOVIDO - Apenas por dentro do painel
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])->defaults('userType', 'admin');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->defaults('userType', 'admin');
});

// Rotas públicas de autenticação - Empresa
Route::prefix('company')->group(function () {
    Route::post('/login', [CompanyAuthController::class, 'login']);
    Route::post('/register', [CompanyAuthController::class, 'register']);
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])->defaults('userType', 'company');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->defaults('userType', 'company');
});

// Rotas públicas de autenticação - Influencer
Route::prefix('influencer')->group(function () {
    Route::post('/login', [InfluencerAuthController::class, 'login']);
    Route::post('/register', [InfluencerAuthController::class, 'register']);
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])->defaults('userType', 'influencer');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])->defaults('userType', 'influencer');
});

// Rota pública de verificação de e-mail
Route::get('/email/verify/{type}/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');

// TESTE PÚBLICO - SEM AUTENTICAÇÃO
Route::get('/test-public', function () {
    return response()->json([
        'message' => 'Rota pública funcionando!',
        'timestamp' => now()
    ]);
});

// ROTA DE TESTE - SEM MIDDLEWARE CUSTOMIZADO
Route::middleware(['auth:sanctum'])->get('/test-auth', function () {
    return response()->json([
        'message' => 'Autenticado com sucesso!',
        'user' => request()->user(),
        'user_type' => get_class(request()->user())
    ]);
});

// Rotas protegidas - Administrador
Route::middleware(['auth:sanctum', 'type.administrator'])->prefix('admin')->group(function () {
    Route::get('/me', [AdministratorAuthController::class, 'me']);
    Route::post('/logout', [AdministratorAuthController::class, 'logout']);
    
    // Perfil
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar']);
    Route::put('/profile', [ProfileController::class, 'updateProfile']);
    
    // Verificação de e-mail
    Route::post('/email/send-verification', [EmailVerificationController::class, 'sendVerificationEmail']);
    Route::get('/email/check-verification', [EmailVerificationController::class, 'checkVerification']);
    
    // Gerenciar Influencers
    Route::get('/influencers', [AdminManagementController::class, 'listInfluencers']);
    Route::get('/influencers/{id}', [AdminManagementController::class, 'showInfluencer']);
    Route::put('/influencers/{id}', [AdminManagementController::class, 'updateInfluencer']);
    Route::delete('/influencers/{id}', [AdminManagementController::class, 'deleteInfluencer']);
    
    // Gerenciar Empresas
    Route::get('/companies', [AdminManagementController::class, 'listCompanies']);
    Route::get('/companies/{id}', [AdminManagementController::class, 'showCompany']);
    Route::put('/companies/{id}', [AdminManagementController::class, 'updateCompany']);
    Route::delete('/companies/{id}', [AdminManagementController::class, 'deleteCompany']);
});

// Rotas protegidas - Empresa
Route::middleware(['auth:sanctum', 'type.company'])->prefix('company')->group(function () {
    Route::get('/me', [CompanyAuthController::class, 'me']);
    Route::post('/logout', [CompanyAuthController::class, 'logout']);
    
    // Perfil
    Route::put('/profile', [CompanyAuthController::class, 'updateProfile']);
    Route::post('/profile/logo', [ProfileController::class, 'uploadLogo']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar']);
    
    // Verificação de e-mail
    Route::post('/email/send-verification', [EmailVerificationController::class, 'sendVerificationEmail']);
    Route::get('/email/check-verification', [EmailVerificationController::class, 'checkVerification']);
    
    // Campanhas
    Route::get('/campaigns/dashboard', [CompanyCampaignController::class, 'dashboard']);
    Route::get('/campaigns', [CompanyCampaignController::class, 'index']);
    Route::post('/campaigns', [CompanyCampaignController::class, 'store']);
    Route::get('/campaigns/{id}', [CompanyCampaignController::class, 'show']);
    Route::put('/campaigns/{id}', [CompanyCampaignController::class, 'update']);
    Route::post('/campaigns/{id}/confirm-payment', [CompanyCampaignController::class, 'confirmPayment']);
    Route::get('/campaigns/{id}/applications', [CompanyCampaignController::class, 'applications']);
    Route::post('/campaigns/{campaignId}/applications/{applicationId}/accept', [CompanyCampaignController::class, 'acceptApplication']);
    Route::post('/campaigns/{campaignId}/applications/{applicationId}/reject', [CompanyCampaignController::class, 'rejectApplication']);
    Route::post('/campaigns/{id}/complete', [CompanyCampaignController::class, 'complete']);
    Route::post('/campaigns/{id}/cancel', [CompanyCampaignController::class, 'cancel']);
    
    // GO Coin
    Route::get('/gocoin/balance', [GoCoinController::class, 'balance']);
    Route::get('/gocoin/transactions', [GoCoinController::class, 'transactions']);
    Route::get('/gocoin/transactions/type/{type}', [GoCoinController::class, 'transactionsByType']);
    Route::get('/gocoin/transactions/category/{category}', [GoCoinController::class, 'transactionsByCategory']);
    Route::post('/gocoin/redeem', [GoCoinController::class, 'redeem']);
    Route::get('/gocoin/stats', [GoCoinController::class, 'stats']);
    Route::get('/gocoin/redeem-categories', [GoCoinController::class, 'redeemCategories']);
    
    // Termos
    Route::post('/terms/confidentiality', [TermsController::class, 'acceptConfidentiality']);
    Route::post('/terms/privacy-policy', [TermsController::class, 'acceptPrivacyPolicy']);
    Route::post('/terms/terms-of-use', [TermsController::class, 'acceptTermsOfUse']);
    Route::get('/terms/status', [TermsController::class, 'status']);
    Route::get('/terms/history', [TermsController::class, 'history']);
    Route::get('/terms/confidentiality/text', [TermsController::class, 'getConfidentialityText']);
    
    // Assinatura
    Route::get('/subscription', [SubscriptionController::class, 'show']);
    Route::post('/subscription', [SubscriptionController::class, 'create']);
    Route::post('/subscription/confirm-payment', [SubscriptionController::class, 'confirmPayment']);
    Route::post('/subscription/renew', [SubscriptionController::class, 'renew']);
    Route::post('/subscription/cancel', [SubscriptionController::class, 'cancel']);
    Route::get('/subscription/status', [SubscriptionController::class, 'status']);
    Route::get('/subscription/payment-history', [SubscriptionController::class, 'paymentHistory']);
});

// Rotas protegidas - Influencer
Route::middleware(['auth:sanctum', 'type.influencer'])->prefix('influencer')->group(function () {
    Route::get('/me', [InfluencerAuthController::class, 'me']);
  
    Route::post('/logout', [InfluencerAuthController::class, 'logout']);
    
    // Perfil
    Route::put('/profile', [InfluencerAuthController::class, 'updateProfile']);
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar']);
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar']);
    
    // Verificação de e-mail
    Route::post('/email/send-verification', [EmailVerificationController::class, 'sendVerificationEmail']);
    Route::get('/email/check-verification', [EmailVerificationController::class, 'checkVerification']);
    
    // Campanhas
    Route::get('/campaigns/dashboard', [InfluencerCampaignController::class, 'dashboard']);
    Route::get('/campaigns/available', [InfluencerCampaignController::class, 'available']);
    Route::get('/campaigns/my-applications', [InfluencerCampaignController::class, 'myApplications']);
    Route::get('/campaigns/my-campaigns', [InfluencerCampaignController::class, 'myCampaigns']);
    Route::get('/campaigns/{id}', [InfluencerCampaignController::class, 'show']);
    Route::post('/campaigns/{id}/apply', [InfluencerCampaignController::class, 'apply']);
    Route::post('/applications/{id}/withdraw', [InfluencerCampaignController::class, 'withdrawApplication']);
    Route::put('/applications/{id}', [InfluencerCampaignController::class, 'updateApplication']);
    Route::get('/my-campaigns/{id}', [InfluencerCampaignController::class, 'showMyCampaign']);
    
    // GO Coin
    Route::get('/gocoin/balance', [GoCoinController::class, 'balance']);
    Route::get('/gocoin/transactions', [GoCoinController::class, 'transactions']);
    Route::get('/gocoin/transactions/type/{type}', [GoCoinController::class, 'transactionsByType']);
    Route::get('/gocoin/transactions/category/{category}', [GoCoinController::class, 'transactionsByCategory']);
    Route::post('/gocoin/redeem', [GoCoinController::class, 'redeem']);
    Route::get('/gocoin/stats', [GoCoinController::class, 'stats']);
    Route::get('/gocoin/redeem-categories', [GoCoinController::class, 'redeemCategories']);
    
    // Termos
    Route::post('/terms/confidentiality', [TermsController::class, 'acceptConfidentiality']);
    Route::post('/terms/privacy-policy', [TermsController::class, 'acceptPrivacyPolicy']);
    Route::post('/terms/terms-of-use', [TermsController::class, 'acceptTermsOfUse']);
    Route::get('/terms/status', [TermsController::class, 'status']);
    Route::get('/terms/history', [TermsController::class, 'history']);
    Route::get('/terms/confidentiality/text', [TermsController::class, 'getConfidentialityText']);
});
