<?php
namespace App\DTO;

use App\Enums\VoucherType;
use App\Enums\VoucherStatus;

class VoucherData
{
    public function __construct(
        public readonly int $id,
        public readonly string $number,
        public readonly string $date,
        public readonly VoucherType $type,
        public readonly int $warehouseId,
        public readonly VoucherStatus $status,
        /** @var VoucherItemData[] */
        public readonly array $items,
    ) {
    }
}