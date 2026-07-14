<?php
namespace App\Actions\Voucher;

use App\Repositories\Contracts\VoucherRepositoryInterface;
use App\Services\StockService;
use App\DTO\VoucherData;
use App\DTO\VoucherItemData;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\InvalidVoucherStatusException;
use App\Enums\VoucherStatus;
use App\Enums\VoucherType;
use Illuminate\Support\Facades\DB;

class ConfirmVoucherAction
{
    public function __construct(
        private VoucherRepositoryInterface $voucherRepo,
        private StockService $stockService,
    ) {
    }

    public function execute(int $voucherId): VoucherData
    {
        $voucher = $this->voucherRepo->findById($voucherId);
        if (!$voucher || $voucher->status !== VoucherStatus::DRAFT) {
            throw new InvalidVoucherStatusException('فقط حواله‌های پیش‌نویس قابل تأیید هستند.');
        }

        DB::transaction(function () use ($voucher) {
            if ($voucher->type === VoucherType::OUTBOUND) {
                foreach ($voucher->items as $item) {
                    $stock = $this->stockService->getCurrentStockWithLock(
                        $item->product_id,
                        $voucher->warehouse_id
                    );
                    if ($stock < $item->quantity) {
                        throw new InsufficientStockException(
                            "موجودی کالا با شناسه {$item->product_id} کافی نیست. (موجودی: {$stock})"
                        );
                    }
                }
            }
            $voucher->status = VoucherStatus::CONFIRMED;
            $voucher->save();
        });

        return $this->mapToDTO($voucher->fresh());
    }

    private function mapToDTO($voucher): VoucherData
    {
        $items = $voucher->items->map(function ($item) {
            return new VoucherItemData(
                productId: $item->product_id,
                productName: $item->product->name,
                quantity: (float) $item->quantity,
            );
        })->toArray();

        return new VoucherData(
            id: $voucher->id,
            number: $voucher->voucher_number,
            date: $voucher->date->toDateTimeString(),
            type: $voucher->type,
            warehouseId: $voucher->warehouse_id,
            status: $voucher->status,
            items: $items,
        );
    }
}