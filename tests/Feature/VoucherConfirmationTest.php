<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Voucher;
use App\Models\VoucherItem;
use App\Enums\VoucherType;
use App\Enums\VoucherStatus;
use App\Exceptions\InsufficientStockException;
use App\Actions\Voucher\ConfirmVoucherAction;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VoucherConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_outbound_voucher_fails_when_stock_insufficient()
    {
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        $voucher = Voucher::factory()
            ->draft()
            ->outbound()
            ->has(VoucherItem::factory()->state([
                'product_id' => $product->id,
                'quantity' => 10,
            ]), 'items')
            ->create(['warehouse_id' => $warehouse->id]);

        $this->expectException(InsufficientStockException::class);

        $action = app(ConfirmVoucherAction::class);
        $action->execute($voucher->id);

        $this->assertDatabaseHas('vouchers', [
            'id' => $voucher->id,
            'status' => VoucherStatus::DRAFT->value,
        ]);
    }

    public function test_outbound_voucher_succeeds_when_stock_sufficient()
    {
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        // یک حواله ورودی تأییدشده برای ایجاد موجودی
        $inbound = Voucher::factory()
            ->confirmed()
            ->inbound()
            ->has(VoucherItem::factory()->state([
                'product_id' => $product->id,
                'quantity' => 100,
            ]), 'items')
            ->create(['warehouse_id' => $warehouse->id]);

        // حواله خروج با مقدار ۱۰
        $outbound = Voucher::factory()
            ->draft()
            ->outbound()
            ->has(VoucherItem::factory()->state([
                'product_id' => $product->id,
                'quantity' => 10,
            ]), 'items')
            ->create(['warehouse_id' => $warehouse->id]);

        $action = app(ConfirmVoucherAction::class);
        $result = $action->execute($outbound->id);

        $this->assertEquals(VoucherStatus::CONFIRMED->value, $result->status->value);
    }
}