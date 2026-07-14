<?php
namespace App\Actions\BOM;

use App\Services\BOMService;
use App\DTO\BOMCostData;
use App\Exceptions\ProductNotFoundException;
use App\Models\Product;

class CalculateBOMCostAction
{
    public function __construct(private BOMService $bomService)
    {
    }

    public function execute(int $productId): BOMCostData
    {
        $product = Product::find($productId);
        if (!$product) {
            throw new ProductNotFoundException("محصول با شناسه {$productId} یافت نشد.");
        }

        $cost = $this->bomService->calculateCost($productId);

        return new BOMCostData(
            productId: $productId,
            productName: $product->name,
            totalCost: $cost,
            calculatedAt: now()->toDateTimeString(),
        );
    }
}