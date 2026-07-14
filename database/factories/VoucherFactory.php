<?php
namespace Database\Factories;
use App\Models\Voucher;
use App\Enums\VoucherType;
use App\Enums\VoucherStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    protected $model = Voucher::class;
    public function definition()
    {
        return [
            'voucher_number' => $this->faker->unique()->bothify('VOU-####'),
            'date' => now(),
            'type' => $this->faker->randomElement([VoucherType::INBOUND, VoucherType::OUTBOUND]),
            'warehouse_id' => \App\Models\Warehouse::factory(),
            'counterparty' => $this->faker->company(),
            'status' => VoucherStatus::DRAFT,
        ];
    }
    public function inbound()
    {
        return $this->state(['type' => VoucherType::INBOUND]);
    }
    public function outbound()
    {
        return $this->state(['type' => VoucherType::OUTBOUND]);
    }
    public function draft()
    {
        return $this->state(['status' => VoucherStatus::DRAFT]);
    }
    public function confirmed()
    {
        return $this->state(['status' => VoucherStatus::CONFIRMED]);
    }
    public function canceled()
    {
        return $this->state(['status' => VoucherStatus::CANCELED]);
    }
}