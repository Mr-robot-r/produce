<?php
namespace App\Services;

use App\Models\Voucher;
use App\Enums\VoucherType;
use App\Enums\VoucherStatus;
use App\Repositories\Contracts\VoucherRepositoryInterface;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function __construct(
        private VoucherRepositoryInterface $voucherRepo
    ) {}

    public function getCurrentStock(int $productId, int $warehouseId): float
    {
        $items = $this->voucherRepo->getConfirmedItems($warehouseId, $productId);
        $stock = 0;
        foreach ($items as $item) {
            $stock += ($item->type === VoucherType::INBOUND->value) ? $item->quantity : -$item->quantity;
        }
        return (float) $stock;
    }

    public function getCurrentStockWithLock(int $productId, int $warehouseId): float
    {
        $result = DB::table('voucher_items')
            ->join('vouchers', 'voucher_items.voucher_id', '=', 'vouchers.id')
            ->where('voucher_items.product_id', $productId)
            ->where('vouchers.warehouse_id', $warehouseId)
            ->where('vouchers.status', VoucherStatus::CONFIRMED->value)
            ->lockForUpdate()
            ->selectRaw('
                SUM(
                    CASE 
                        WHEN vouchers.type = ? THEN voucher_items.quantity 
                        ELSE -voucher_items.quantity 
                    END
                ) as total
            ', [VoucherType::INBOUND->value])
            ->first();

        return (float) ($result->total ?? 0);
    }

    public function getHistory(int $productId, int $warehouseId): array
    {
        return DB::table('voucher_items')
            ->join('vouchers', 'voucher_items.voucher_id', '=', 'vouchers.id')
            ->where('voucher_items.product_id', $productId)
            ->where('vouchers.warehouse_id', $warehouseId)
            ->whereIn('vouchers.status', [VoucherStatus::CONFIRMED->value, VoucherStatus::CANCELED->value])
            ->orderBy('vouchers.date')
            ->get(['vouchers.voucher_number', 'vouchers.date', 'vouchers.type', 'voucher_items.quantity'])
            ->map(function ($item) {
                return [
                    'voucher_number' => $item->voucher_number,
                    'date' => $item->date,
                    'quantity' => $item->quantity,
                    'change' => $item->type === VoucherType::INBOUND->value ? +$item->quantity : -$item->quantity,
                ];
            })
            ->toArray();
    }
}