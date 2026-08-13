<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Invoice;

class ReportApiController extends Controller
{
    public function index()
    {
        $revenue = Payment::sum('amount');
        $expenses = Expense::sum('amount');
        return response()->json([
            'stats' => [
                'customers' => Customer::count(),
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $revenue - $expenses,
                'invoices' => Invoice::count(),
                'paid' => Invoice::where('status','paid')->count(),
            ],
        ]);
    }
}
