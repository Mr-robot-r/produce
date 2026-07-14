<?php
namespace App\Repositories\Contracts;

interface BOMRepositoryInterface
{
    public function loadBOMTree(int $productId): array;
    public function hasCycle(int $productId): bool;
}