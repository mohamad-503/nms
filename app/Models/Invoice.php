<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = ['invoice_number', 'customer_id', 'plan_id', 'amount', 'tax', 'total', 'status', 'issued_date', 'due_date', 'notes'];

    protected $casts = ['amount' => 'decimal:2', 'tax' => 'decimal:2', 'total' => 'decimal:2', 'issued_date' => 'date', 'due_date' => 'date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
