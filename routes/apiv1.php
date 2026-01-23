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
| API Routes - Version 1
|--------------------------------------------------------------------------
|
| Rotas da API versão 1.0
| Base URL: /api/v1
|
*/

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/ping', function () {
    return response()->json(['pong' => true]);
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Verificação de e-mail (link público)
Route::get('/email/verify/{type}/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');

/*
|--------------------------------------------------------------------------
| Autenticação - Administrator
|--------------------------------------------------------------------------
*/

Route::prefix('admin/auth')->group(function () {
    // Rotas públicas
    Route::post('/login', [AdministratorAuthController::class, 'login']);
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])
        ->defaults('userType', 'admin');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
        ->defaults('userType', 'admin');
    
    // Rotas protegidas
    Route::middleware(['auth:sanctum', 'type.administrator'])->group(function () {
        Route::get('/me', [AdministratorAuthController::class, 'me']);
        Route::post('/logout', [AdministratorAuthController::class, 'logout']);
    });
});

/*
|--------------------------------------------------------------------------
| Autenticação - Company
|--------------------------------------------------------------------------
*/

Route::prefix('company/auth')->group(function () {
    // Rotas públicas
    Route::post('/register', [CompanyAuthController::class, 'register']);
    Route::post('/login', [CompanyAuthController::class, 'login']);
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])
        ->defaults('userType', 'company');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
        ->defaults('userType', 'company');
    
    // Rotas protegidas
    Route::middleware(['auth:sanctum', 'type.company'])->group(function () {
        Route::get('/me', [CompanyAuthController::class, 'me']);
        Route::post('/logout', [CompanyAuthController::class, 'logout']);
    });
});

/*
|--------------------------------------------------------------------------
| Autenticação - Influencer
|--------------------------------------------------------------------------
*/

Route::prefix('influencer/auth')->group(function () {
    // Rotas públicas
    Route::post('/register', [InfluencerAuthController::class, 'register']);
    Route::post('/login', [InfluencerAuthController::class, 'login']);
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword'])
        ->defaults('userType', 'influencer');
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
        ->defaults('userType', 'influencer');
    
    // Rotas protegidas
    Route::middleware(['auth:sanctum', 'type.influencer'])->group(function () {
        Route::get('/me', [InfluencerAuthController::class, 'me']);
        Route::post('/logout', [InfluencerAuthController::class, 'logout']);
    });
});

/*
|--------------------------------------------------------------------------
| Administrator - Área Protegida
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'type.administrator'])->prefix('admin')->group(function () {
    
    // Perfil
    Route::prefix('profile')->group(function () {
        Route::put('/', [ProfileController::class, 'updateProfile']);
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar']);
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar']);
    });
    
    // Verificação de e-mail
    Route::prefix('email')->group(function () {
        Route::post('/send-verification', [EmailVerificationController::class, 'sendVerificationEmail']);
        Route::get('/check-verification', [EmailVerificationController::class, 'checkVerification']);
    });
    
    // Gerenciamento de Influencers
    Route::prefix('influencers')->group(function () {
        Route::get('/', [AdminManagementController::class, 'listInfluencers']);
        Route::get('/{id}', [AdminManagementController::class, 'showInfluencer']);
        Route::put('/{id}', [AdminManagementController::class, 'updateInfluencer']);
        Route::delete('/{id}', [AdminManagementController::class, 'deleteInfluencer']);
    });
    
    // Gerenciamento de Empresas
    Route::prefix('companies')->group(function () {
        Route::get('/', [AdminManagementController::class, 'listCompanies']);
        Route::get('/{id}', [AdminManagementController::class, 'showCompany']);
        Route::put('/{id}', [AdminManagementController::class, 'updateCompany']);
        Route::delete('/{id}', [AdminManagementController::class, 'deleteCompany']);
    });
});

/*
|--------------------------------------------------------------------------
| Company - Área Protegida
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'type.company'])->prefix('company')->group(function () {
    
    // Perfil
    Route::prefix('profile')->group(function () {
        Route::put('/', [CompanyAuthController::class, 'updateProfile']);
        Route::post('/logo', [ProfileController::class, 'uploadLogo']);
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar']);
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar']);
    });
    
    // Verificação de e-mail
    Route::prefix('email')->group(function () {
        Route::post('/send-verification', [EmailVerificationController::class, 'sendVerificationEmail']);
        Route::get('/check-verification', [EmailVerificationController::class, 'checkVerification']);
    });
    
    // Campanhas
    Route::prefix('campaigns')->group(function () {
        Route::get('/dashboard', [CompanyCampaignController::class, 'dashboard']);
        Route::get('/', [CompanyCampaignController::class, 'index']);
        Route::post('/', [CompanyCampaignController::class, 'store']);
        Route::get('/{id}', [CompanyCampaignController::class, 'show']);
        Route::put('/{id}', [CompanyCampaignController::class, 'update']);
        Route::post('/{id}/confirm-payment', [CompanyCampaignController::class, 'confirmPayment']);
        Route::post('/{id}/complete', [CompanyCampaignController::class, 'complete']);
        Route::post('/{id}/cancel', [CompanyCampaignController::class, 'cancel']);
        
        // Applications
        Route::get('/{id}/applications', [CompanyCampaignController::class, 'applications']);
        Route::post('/{campaignId}/applications/{applicationId}/accept', [CompanyCampaignController::class, 'acceptApplication']);
        Route::post('/{campaignId}/applications/{applicationId}/reject', [CompanyCampaignController::class, 'rejectApplication']);
    });
    
    // GO Coin
    Route::prefix('gocoin')->group(function () {
        Route::get('/balance', [GoCoinController::class, 'balance']);
        Route::get('/stats', [GoCoinController::class, 'stats']);
        Route::get('/transactions', [GoCoinController::class, 'transactions']);
        Route::get('/transactions/type/{type}', [GoCoinController::class, 'transactionsByType']);
        Route::get('/transactions/category/{category}', [GoCoinController::class, 'transactionsByCategory']);
        Route::post('/redeem', [GoCoinController::class, 'redeem']);
        Route::get('/redeem-categories', [GoCoinController::class, 'redeemCategories']);
    });
    
    // Termos
    Route::prefix('terms')->group(function () {
        Route::get('/status', [TermsController::class, 'status']);
        Route::get('/history', [TermsController::class, 'history']);
        Route::get('/confidentiality/text', [TermsController::class, 'getConfidentialityText']);
        Route::post('/confidentiality', [TermsController::class, 'acceptConfidentiality']);
        Route::post('/privacy-policy', [TermsController::class, 'acceptPrivacyPolicy']);
        Route::post('/terms-of-use', [TermsController::class, 'acceptTermsOfUse']);
    });
    
    // Assinatura
    Route::prefix('subscription')->group(function () {
        Route::get('/', [SubscriptionController::class, 'show']);
        Route::get('/status', [SubscriptionController::class, 'status']);
        Route::get('/payment-history', [SubscriptionController::class, 'paymentHistory']);
        Route::post('/', [SubscriptionController::class, 'create']);
        Route::post('/confirm-payment', [SubscriptionController::class, 'confirmPayment']);
        Route::post('/renew', [SubscriptionController::class, 'renew']);
        Route::post('/cancel', [SubscriptionController::class, 'cancel']);
    });
});

/*
|--------------------------------------------------------------------------
| Influencer - Área Protegida
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'type.influencer'])->prefix('influencer')->group(function () {
    
    // Perfil
    Route::prefix('profile')->group(function () {
        Route::put('/', [InfluencerAuthController::class, 'updateProfile']);
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar']);
        Route::delete('/avatar', [ProfileController::class, 'deleteAvatar']);
    });
    
    // Verificação de e-mail
    Route::prefix('email')->group(function () {
        Route::post('/send-verification', [EmailVerificationController::class, 'sendVerificationEmail']);
        Route::get('/check-verification', [EmailVerificationController::class, 'checkVerification']);
    });
    
    // Campanhas
    Route::prefix('campaigns')->group(function () {
        Route::get('/dashboard', [InfluencerCampaignController::class, 'dashboard']);
        Route::get('/available', [InfluencerCampaignController::class, 'available']);
        Route::get('/my-applications', [InfluencerCampaignController::class, 'myApplications']);
        Route::get('/my-campaigns', [InfluencerCampaignController::class, 'myCampaigns']);
        Route::get('/{id}', [InfluencerCampaignController::class, 'show']);
        Route::post('/{id}/apply', [InfluencerCampaignController::class, 'apply']);
        Route::get('/active/{id}', [InfluencerCampaignController::class, 'showMyCampaign']);
    });
    
    // Applications
    Route::prefix('applications')->group(function () {
        Route::put('/{id}', [InfluencerCampaignController::class, 'updateApplication']);
        Route::post('/{id}/withdraw', [InfluencerCampaignController::class, 'withdrawApplication']);
    });
    
    // GO Coin
    Route::prefix('gocoin')->group(function () {
        Route::get('/balance', [GoCoinController::class, 'balance']);
        Route::get('/stats', [GoCoinController::class, 'stats']);
        Route::get('/transactions', [GoCoinController::class, 'transactions']);
        Route::get('/transactions/type/{type}', [GoCoinController::class, 'transactionsByType']);
        Route::get('/transactions/category/{category}', [GoCoinController::class, 'transactionsByCategory']);
        Route::post('/redeem', [GoCoinController::class, 'redeem']);
        Route::get('/redeem-categories', [GoCoinController::class, 'redeemCategories']);
    });
    
    // Termos
    Route::prefix('terms')->group(function () {
        Route::get('/status', [TermsController::class, 'status']);
        Route::get('/history', [TermsController::class, 'history']);
        Route::get('/confidentiality/text', [TermsController::class, 'getConfidentialityText']);
        Route::post('/confidentiality', [TermsController::class, 'acceptConfidentiality']);
        Route::post('/privacy-policy', [TermsController::class, 'acceptPrivacyPolicy']);
        Route::post('/terms-of-use', [TermsController::class, 'acceptTermsOfUse']);
    });
});
