<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashBoxTransaction extends Model
{
    protected $table = 'cash_box_transactions';

    protected $fillable = ['type', 'amount', 'source', 'reference', 'transaction_date', 'notes'];

    protected $casts = ['amount' => 'decimal:2', 'transaction_date' => 'date'];
}
