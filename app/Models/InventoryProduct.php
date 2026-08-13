<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryProduct extends Model
{
    protected $fillable = ['category_id', 'supplier_id', 'name', 'sku', 'cost_price', 'sale_price', 'quantity', 'min_quantity', 'unit'];

    protected $casts = ['cost_price' => 'decimal:2', 'sale_price' => 'decimal:2'];

    public function category()
    {
        return $this->belongsTo(InventoryCategory::class, 'category_id');
    }

    public function supplier()
    {
        return $this->belongsTo(InventorySupplier::class, 'supplier_id');
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class, 'product_id');
    }

    public function serials()
    {
        return $this->hasMany(ProductSerial::class, 'product_id');
    }
}
