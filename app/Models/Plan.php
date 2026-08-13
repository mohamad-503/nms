<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = ['name', 'price', 'download_speed', 'upload_speed', 'burst', 'validity', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
