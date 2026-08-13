<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    protected $fillable = ['customer_id', 'amount', 'paid_amount', 'status', 'due_date', 'notes'];

    protected $casts = ['amount' => 'decimal:2', 'paid_amount' => 'decimal:2', 'due_date' => 'date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
