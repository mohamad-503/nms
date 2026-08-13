<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'full_name', 'phone', 'national_id', 'address',
        'city_id', 'area_id', 'tower_id',
        'pppoe_username', 'pppoe_password', 'plan_id',
        'download_speed', 'upload_speed', 'static_ip', 'mac_address',
        'installation_date', 'subscription_start', 'subscription_end', 'monthly_price',
        'status', 'notes', 'balance', 'profile_photo',
    ];

    protected $casts = [
        'installation_date' => 'date',
        'subscription_start' => 'date',
        'subscription_end' => 'date',
        'monthly_price' => 'decimal:2',
        'balance' => 'decimal:2',
    ];

    public function city() { return $this->belongsTo(City::class); }
    public function area() { return $this->belongsTo(Area::class); }
    public function tower() { return $this->belongsTo(Tower::class); }
    public function plan() { return $this->belongsTo(Plan::class); }
    public function invoices() { return $this->hasMany(Invoice::class); }
    public function tickets() { return $this->hasMany(SupportTicket::class); }
    public function debts() { return $this->hasMany(Debt::class); }
}
