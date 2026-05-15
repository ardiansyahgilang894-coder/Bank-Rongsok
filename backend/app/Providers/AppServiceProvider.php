<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Activity;
use App\Models\Fund;
use App\Models\ScrapSale;
use App\Models\DonationDistribution;
use App\Policies\ActivityPolicy;
use App\Policies\FundPolicy;
use App\Policies\ScrapSalePolicy;
use App\Policies\DonationDistributionPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Activity::class, ActivityPolicy::class);
        Gate::policy(Fund::class, FundPolicy::class);
        Gate::policy(ScrapSale::class, ScrapSalePolicy::class);
        Gate::policy(DonationDistribution::class, DonationDistributionPolicy::class);
    }
}
