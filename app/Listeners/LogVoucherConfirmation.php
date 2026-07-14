<?php
namespace App\Listeners;

use App\Events\VoucherConfirmed;
use Illuminate\Support\Facades\Log;

class LogVoucherConfirmation
{
    public function handle(VoucherConfirmed $event): void
    {
        Log::info("حواله تأیید شد: شماره {$event->voucher->voucher_number}");
    }
}