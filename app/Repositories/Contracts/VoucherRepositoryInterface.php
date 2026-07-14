<?php
namespace App\Repositories\Contracts;

use App\Models\Voucher;
use Illuminate\Support\Collection;

interface VoucherRepositoryInterface
{
    public function findById(int $id): ?Voucher;
    public function getConfirmedItems(int $warehouseId, int $productId): Collection;
}