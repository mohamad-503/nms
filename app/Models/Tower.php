<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tower extends Model
{
    protected $fillable = ['area_id', 'name', 'ip'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
