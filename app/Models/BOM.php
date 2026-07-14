<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BOM extends Model
{
    use HasFactory ;
    protected $table = "boms";
    protected $fillable = ['parent_product_id', 'child_product_id', 'quantity'];
    protected $casts = ['quantity' => 'decimal:2'];

    public function parentProduct()
    {
        return $this->belongsTo(Product::class, 'parent_product_id');
    }
    public function childProduct()
    {
        return $this->belongsTo(Product::class, 'child_product_id');
    }
}