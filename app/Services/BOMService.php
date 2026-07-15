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
    ) {
    }

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

    public function getBOMTree(int $productId): array
    {
        $product = Product::find($productId);
        if (!$product) {
            throw new \Exception("محصول {$productId} یافت نشد.");
        }

        $tree = $this->bomRepo->loadBOMTree($productId);
        return $this->buildTreeWithCost($product, $tree, 1);
    }

    private function buildTreeWithCost(Product $product, array $childrenData, float $quantity): array
    {
        $children = [];
        $childrenTotalCost = 0;

        foreach ($childrenData as $branch) {
            /** @var \App\Models\BOM $bom */
            $bom = $branch['bom'];
            $childProduct = $bom->childProduct;

            $childNode = $this->buildTreeWithCost(
                $childProduct,
                $branch['children'],
                $bom->quantity
            );

            // هزینه‌ی هر واحد از محصول فرزند (قیمت پایه + هزینه‌ی زیرمجموعه‌ها)
            if (!empty($branch['children'])) {
                $childUnitCost = $this->calculateTreeCost($branch['children']);
            } else {
                $childUnitCost = $childProduct->base_price ?? 0; // برگ: هزینه برابر base_price
            }
            $branchTotalCost = $bom->quantity * $childUnitCost;

            $children[] = [
                'product_id' => $childProduct->id,
                'product_name' => $childProduct->name,
                'unit' => $childProduct->unit,
                'base_price' => $childProduct->base_price,
                'quantity' => $bom->quantity,
                'unit_cost' => $childUnitCost,
                'branch_total_cost' => $branchTotalCost,
                'children' => $childNode['children'] ?? [],
            ];

            $childrenTotalCost += $branchTotalCost;
        }

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'unit' => $product->unit,
            'base_price' => $product->base_price,
            'quantity' => $quantity,
            'children_total_cost' => $childrenTotalCost,
            'total_cost' => $childrenTotalCost,
            'children' => $children,
        ];
    }

}