<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\BOM;
use App\Models\Voucher;
use App\Models\VoucherItem;
use App\Enums\VoucherType;
use App\Enums\VoucherStatus;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // انبارها
        $wh1 = Warehouse::create(['name' => 'انبار اصلی']);
        $wh2 = Warehouse::create(['name' => 'انبار فرعی']);

        // محصولات
        $pA = Product::create(['name' => 'محصول نهایی A', 'unit' => 'عدد', 'base_price' => 10000]);
        $pB = Product::create(['name' => 'قطعه B', 'unit' => 'کیلو', 'base_price' => 5000]);
        $pC = Product::create(['name' => 'قطعه C', 'unit' => 'متر', 'base_price' => 3000]);
        $pD = Product::create(['name' => 'مواد اولیه D', 'unit' => 'لیتر', 'base_price' => 1000]);
        $pE = Product::create(['name' => 'مواد اولیه E', 'unit' => 'عدد', 'base_price' => 2000]);

        // BOM: A = 2*B + 1*C , B = 3*D + 1*E
        BOM::create(['parent_product_id' => $pA->id, 'child_product_id' => $pB->id, 'quantity' => 2]);
        BOM::create(['parent_product_id' => $pA->id, 'child_product_id' => $pC->id, 'quantity' => 1]);
        BOM::create(['parent_product_id' => $pB->id, 'child_product_id' => $pD->id, 'quantity' => 3]);
        BOM::create(['parent_product_id' => $pB->id, 'child_product_id' => $pE->id, 'quantity' => 1]);

        // حواله ورودی نمونه (تأییدشده)
        $voucher = Voucher::create([
            'voucher_number' => 'IN-001',
            'date' => now(),
            'type' => VoucherType::INBOUND,
            'warehouse_id' => $wh1->id,
            'counterparty' => 'تأمین‌کننده X',
            'status' => VoucherStatus::CONFIRMED,
        ]);

        VoucherItem::create(['voucher_id' => $voucher->id, 'product_id' => $pB->id, 'quantity' => 100]);
        VoucherItem::create(['voucher_id' => $voucher->id, 'product_id' => $pC->id, 'quantity' => 50]);
        VoucherItem::create(['voucher_id' => $voucher->id, 'product_id' => $pD->id, 'quantity' => 200]);
        VoucherItem::create(['voucher_id' => $voucher->id, 'product_id' => $pE->id, 'quantity' => 100]);
    }
}