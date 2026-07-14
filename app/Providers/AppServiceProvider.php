<?php

namespace App\Providers;

use App\Repositories\Contracts\BOMRepositoryInterface;
use App\Repositories\Contracts\VoucherRepositoryInterface;
use App\Repositories\Eloquent\BOMRepository;
use App\Repositories\Eloquent\VoucherRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // اتصال ریپازیتوری‌ها
        $this->app->bind(BOMRepositoryInterface::class, BOMRepository::class);
        $this->app->bind(VoucherRepositoryInterface::class, VoucherRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
