<?php


namespace App\DTO;

class BOMTreeNode
{
    public function __construct(
        public int $productId,
        public string $productName,
        public string $unit,
        public float $basePrice,
        public float $quantity, // مقدار مصرف در والد
        public array $children = [], // آرایه‌ای از BOMTreeNode
    ) {
    }
}