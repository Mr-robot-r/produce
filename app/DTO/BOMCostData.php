<?php
namespace App\DTO;

class BOMCostData
{
    public function __construct(
        public readonly int $productId,
        public readonly string $productName,
        public readonly float $totalCost,
        public readonly string $calculatedAt,
    ) {
    }
}