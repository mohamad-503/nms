<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryCategory extends Model
{
    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(InventoryProduct::class, 'category_id');
    }
}
