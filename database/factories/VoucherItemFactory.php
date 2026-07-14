<?php
namespace Database\Factories;
use App\Models\VoucherItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherItemFactory extends Factory
{
    protected $model = VoucherItem::class;
    public function definition()
    {
        return [
            'voucher_id' => \App\Models\Voucher::factory(),
            'product_id' => \App\Models\Product::factory(),
            'quantity' => $this->faker->randomFloat(2, 1, 100),
        ];
    }
}