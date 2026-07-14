<?php
namespace App\Models;

use App\Enums\VoucherStatus;
use App\Enums\VoucherType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory ;

    protected $fillable = ['voucher_number', 'date', 'type', 'warehouse_id', 'counterparty', 'status'];
    protected $casts = [
        'date' => 'datetime',
        'type' => VoucherType::class,
        'status' => VoucherStatus::class,
    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function items()
    {
        return $this->hasMany(VoucherItem::class);
    }
}