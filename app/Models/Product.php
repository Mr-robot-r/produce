<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory ;

    protected $fillable = ['name', 'unit', 'base_price'];
    protected $casts = ['base_price' => 'decimal:2'];

    // روابط BOM
    public function bomsAsParent()
    {
        return $this->hasMany(BOM::class, 'parent_product_id');
    }
    public function bomsAsChild()
    {
        return $this->hasMany(BOM::class, 'child_product_id');
    }
    public function components()
    {
        return $this->hasMany(BOM::class, 'parent_product_id')->with('childProduct');
    }
}
