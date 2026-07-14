<?php
namespace App\Services;

use App\Repositories\Contracts\BOMRepositoryInterface;
use App\Exceptions\CyclicBOMException;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class BOMService
{
    private const CACHE_TTL = 3600;

    public function __construct(
        private BOMRepositoryInterface $bomRepo
    ) {}

    public function calculateCost(int $productId): float
    {
        $cacheKey = "bom_cost_{$productId}";
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($productId) {
            $tree = $this->bomRepo->loadBOMTree($productId);
            return $this->calculateTreeCost($tree);
        });
    }

    private function calculateTreeCost(array $tree): float
    {
        $total = 0;
        foreach ($tree as $branch) {
            /** @var \App\Models\BOM $bom */
            $bom = $branch['bom'];
            $childPrice = $bom->childProduct->base_price ?? 0;
            // اگر فرزند خودش زیرمجموعه داشت، هزینه آن را محاسبه کن
            if (!empty($branch['children'])) {
                $childPrice = $this->calculateTreeCost($branch['children']);
            }
            $total += $bom->quantity * $childPrice;
        }
        return round($total, 2);
    }

    public function validateBOM(int $productId): void
    {
        if ($this->bomRepo->hasCycle($productId)) {
            throw new CyclicBOMException("حلقه در ساختار BOM محصول {$productId} شناسایی شد.");
        }
        Cache::forget("bom_cost_{$productId}");
    }
}