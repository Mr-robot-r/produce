<?php
namespace App\Actions\BOM;

use App\Models\BOM;
use App\Services\BOMService;
use App\Exceptions\CyclicBOMException;
use App\Exceptions\ProductNotFoundException;
use App\Models\Product;

class AddBOMAction
{
    public function __construct(private BOMService $bomService) {}

    public function execute(int $parentId, int $childId, float $quantity): BOM
    {
        if (!Product::find($parentId) || !Product::find($childId)) {
            throw new ProductNotFoundException('محصول والد یا فرزند یافت نشد.');
        }

        $bom = BOM::create([
            'parent_product_id' => $parentId,
            'child_product_id' => $childId,
            'quantity' => $quantity,
        ]);

        try {
            $this->bomService->validateBOM($parentId);
        } catch (CyclicBOMException $e) {
            $bom->delete();
            throw $e;
        }

        return $bom;
    }
}