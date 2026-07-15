<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\BOM;
use App\Actions\BOM\CalculateBOMCostAction;
use App\Actions\BOM\AddBOMAction;
use App\Exceptions\CyclicBOMException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BOMCostTest extends TestCase
{
    use RefreshDatabase;

    public function test_multi_level_bom_cost_calculation()
    {
        // ایجاد محصولات با قیمت‌های مشخص
        $pA = Product::create(['name' => 'A', 'unit' => 'عدد', 'base_price' => 10]);
        $pB = Product::create(['name' => 'B', 'unit' => 'کیلو', 'base_price' => 20]);
        $pC = Product::create(['name' => 'C', 'unit' => 'متر', 'base_price' => 30]);
        $pD = Product::create(['name' => 'D', 'unit' => 'لیتر', 'base_price' => 40]);
        $pE = Product::create(['name' => 'E', 'unit' => 'عدد', 'base_price' => 50]); // E را هم اضافه کنید

        // تعریف BOM
        BOM::create(['parent_product_id' => $pA->id, 'child_product_id' => $pB->id, 'quantity' => 2]);
        BOM::create(['parent_product_id' => $pA->id, 'child_product_id' => $pC->id, 'quantity' => 1]);
        BOM::create(['parent_product_id' => $pB->id, 'child_product_id' => $pD->id, 'quantity' => 3]);
        BOM::create(['parent_product_id' => $pB->id, 'child_product_id' => $pE->id, 'quantity' => 1]);

        $action = app(CalculateBOMCostAction::class);
        $costData = $action->execute($pA->id);

        // محاسبه دستی: A =  2*(3*40 + 1*50) + 1*30 = 2*(120+50) + 30 = 2*170 + 30 = 340+30 = 370

        $this->assertEquals(370, $costData->totalCost);
    }

    public function test_cyclic_bom_detection_prevents_saving()
    {
        $p1 = Product::factory()->create();
        $p2 = Product::factory()->create();
        $p3 = Product::factory()->create();

        BOM::create(['parent_product_id' => $p1->id, 'child_product_id' => $p2->id, 'quantity' => 1]);
        BOM::create(['parent_product_id' => $p2->id, 'child_product_id' => $p3->id, 'quantity' => 1]);

        $this->expectException(CyclicBOMException::class);
        $addAction = app(AddBOMAction::class);
        $addAction->execute($p3->id, $p1->id, 1);
    }
}