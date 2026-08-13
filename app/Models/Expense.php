<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['category', 'amount', 'expense_date', 'description'];

    protected $casts = ['amount' => 'decimal:2', 'expense_date' => 'date'];
}
