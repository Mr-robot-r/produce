<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\VoucherConfirmed;
use App\Listeners\LogVoucherConfirmation;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        VoucherConfirmed::class => [
            LogVoucherConfirmation::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}