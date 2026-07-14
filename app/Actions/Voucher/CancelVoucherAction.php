<?php
namespace App\Actions\Voucher;

use App\Repositories\Contracts\VoucherRepositoryInterface;
use App\Exceptions\InvalidVoucherStatusException;
use App\Enums\VoucherStatus;
use Illuminate\Support\Facades\DB;

class CancelVoucherAction
{
    public function __construct(
        private VoucherRepositoryInterface $voucherRepo,
    ) {}

    public function execute(int $voucherId): void
    {
        $voucher = $this->voucherRepo->findById($voucherId);
        if (!$voucher || $voucher->status !== VoucherStatus::CONFIRMED) {
            throw new InvalidVoucherStatusException('فقط حواله‌های تأییدشده قابل لغو هستند.');
        }

        DB::transaction(function () use ($voucher) {
            $voucher->status = VoucherStatus::CANCELED;
            $voucher->save();
        });
    }
}