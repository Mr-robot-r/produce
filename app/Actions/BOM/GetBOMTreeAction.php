<?php
// app/Actions/BOM/GetBOMTreeAction.php
namespace App\Actions\BOM;

use App\DTO\BOMTreeNode;
use App\Models\Product;
use App\Exceptions\ProductNotFoundException;
use App\Services\BOMService;

class GetBOMTreeAction
{
    public function __construct(private BOMService $bomService)
    {
    }

    public function execute(int $productId): array
    {
        $product = Product::find($productId);
        if (!$product) {
            throw new ProductNotFoundException("محصول {$productId} یافت نشد.");
        }

        return $this->bomService->getBOMTree($productId);
    }
}