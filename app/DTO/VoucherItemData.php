<?php
namespace App\DTO;

class VoucherItemData
{
    public function __construct(
        public readonly int $productId,
        public readonly string $productName,
        public readonly float $quantity,
    ) {}
}