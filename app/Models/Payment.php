<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['invoice_id', 'customer_id', 'amount', 'method', 'paid_date', 'notes'];

    protected $casts = ['amount' => 'decimal:2', 'paid_date' => 'date'];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
