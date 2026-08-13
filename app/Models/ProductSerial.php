<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSerial extends Model
{
    protected $fillable = ['product_id', 'serial', 'status'];

    public function product()
    {
        return $this->belongsTo(InventoryProduct::class, 'product_id');
    }
}
