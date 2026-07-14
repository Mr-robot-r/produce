<?php
namespace App\Events;

use App\Models\Voucher;
use Illuminate\Foundation\Events\Dispatchable;

class VoucherConfirmed
{
    use Dispatchable;

    public function __construct(public Voucher $voucher)
    {
    }
}