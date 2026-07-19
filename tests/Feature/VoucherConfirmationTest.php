<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\Voucher;
use App\Models\VoucherItem;
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
            ->has(
                VoucherItem::factory()->state([
                    'product_id' => $product->id,
                    'quantity' => 10,
                ]),
                'items'
            )
            ->create([
                'warehouse_id' => $warehouse->id,
            ]);

        $action = app(ConfirmVoucherAction::class);

        try {
            $action->execute($voucher->id);

            $this->fail('Expected InsufficientStockException was not thrown.');
        } catch (InsufficientStockException $e) {
            $this->assertInstanceOf(
                InsufficientStockException::class,
                $e
            );
        }

        // وضعیت حواله نباید تغییر کرده باشد
        $this->assertDatabaseHas('vouchers', [
            'id' => $voucher->id,
            'status' => VoucherStatus::DRAFT->value,
        ]);

        // اطمینان از رفرش شدن مدل
        $voucher->refresh();

        $this->assertEquals(
            VoucherStatus::DRAFT,
            $voucher->status
        );

        // در صورت داشتن جدول موجودی می‌توانید این بخش را نیز اضافه کنید:
        /*
        $this->assertDatabaseMissing('warehouse_stocks', [
            'warehouse_id' => $warehouse->id,
            'product_id'   => $product->id,
        ]);
        */
    }

    public function test_outbound_voucher_succeeds_when_stock_sufficient()
    {
        $product = Product::factory()->create();
        $warehouse = Warehouse::factory()->create();

        // ایجاد موجودی اولیه
        Voucher::factory()
            ->confirmed()
            ->inbound()
            ->has(
                VoucherItem::factory()->state([
                    'product_id' => $product->id,
                    'quantity' => 100,
                ]),
                'items'
            )
            ->create([
                'warehouse_id' => $warehouse->id,
            ]);

        // حواله خروج
        $outbound = Voucher::factory()
            ->draft()
            ->outbound()
            ->has(
                VoucherItem::factory()->state([
                    'product_id' => $product->id,
                    'quantity' => 10,
                ]),
                'items'
            )
            ->create([
                'warehouse_id' => $warehouse->id,
            ]);

        $action = app(ConfirmVoucherAction::class);

        $result = $action->execute($outbound->id);

        $this->assertEquals(
            VoucherStatus::CONFIRMED,
            $result->status
        );

        $this->assertDatabaseHas('vouchers', [
            'id' => $outbound->id,
            'status' => VoucherStatus::CONFIRMED->value,
        ]);

        $outbound->refresh();

        $this->assertEquals(
            VoucherStatus::CONFIRMED,
            $outbound->status
        );
    }
}