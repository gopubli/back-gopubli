<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// Repositories
use App\Repositories\Admin\CampaignRepository;
use App\Repositories\Admin\ApplicationRepository;
use App\Repositories\Admin\InfluencerRepository;
use App\Repositories\Admin\CompanyRepository;
use App\Repositories\Admin\GoCoinRepository;

// Services
use App\Services\Admin\CampaignManagementService;
use App\Services\Admin\ApplicationManagementService;
use App\Services\Admin\UserManagementService;
use App\Services\Admin\GoCoinManagementService;
use App\Services\Admin\ReportService;

// Models
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\Influencer;
use App\Models\Company;
use App\Models\GoCoinTransaction;

class AdminServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Repositories
        $this->app->bind(CampaignRepository::class, function ($app) {
            return new CampaignRepository(new Campaign());
        });

        $this->app->bind(ApplicationRepository::class, function ($app) {
            return new ApplicationRepository(new CampaignApplication());
        });

        $this->app->bind(InfluencerRepository::class, function ($app) {
            return new InfluencerRepository(new Influencer());
        });

        $this->app->bind(CompanyRepository::class, function ($app) {
            return new CompanyRepository(new Company());
        });

        $this->app->bind(GoCoinRepository::class, function ($app) {
            return new GoCoinRepository(new GoCoinTransaction());
        });

        // Register Services
        $this->app->bind(CampaignManagementService::class, function ($app) {
            return new CampaignManagementService(
                $app->make(CampaignRepository::class)
            );
        });

        $this->app->bind(ApplicationManagementService::class, function ($app) {
            return new ApplicationManagementService(
                $app->make(ApplicationRepository::class)
            );
        });

        $this->app->bind(UserManagementService::class, function ($app) {
            return new UserManagementService(
                $app->make(InfluencerRepository::class),
                $app->make(CompanyRepository::class)
            );
        });

        $this->app->bind(GoCoinManagementService::class, function ($app) {
            return new GoCoinManagementService(
                $app->make(GoCoinRepository::class)
            );
        });

        $this->app->bind(ReportService::class, function ($app) {
            return new ReportService(
                $app->make(CampaignRepository::class),
                $app->make(CompanyRepository::class),
                $app->make(InfluencerRepository::class),
                $app->make(ApplicationRepository::class),
                $app->make(GoCoinRepository::class)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
