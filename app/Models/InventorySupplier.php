<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventorySupplier extends Model
{
    protected $fillable = ['name', 'phone', 'email', 'address'];

    public function products()
    {
        return $this->hasMany(InventoryProduct::class, 'supplier_id');
    }
}
