<?php
namespace App\Repositories\Eloquent;

use App\Models\Voucher;
use App\Models\VoucherItem;
use App\Repositories\Contracts\VoucherRepositoryInterface;
use App\Enums\VoucherStatus;
use Illuminate\Support\Collection;

class VoucherRepository implements VoucherRepositoryInterface
{
    public function findById(int $id): ?Voucher
    {
        return Voucher::with('items.product')->find($id);
    }

    public function getConfirmedItems(int $warehouseId, int $productId): Collection
    {
        return VoucherItem::query()
            ->join('vouchers', 'voucher_items.voucher_id', '=', 'vouchers.id')
            ->where('vouchers.warehouse_id', $warehouseId)
            ->where('voucher_items.product_id', $productId)
            ->where('vouchers.status', VoucherStatus::CONFIRMED->value)
            ->select('voucher_items.*', 'vouchers.type')
            ->get();
    }
}