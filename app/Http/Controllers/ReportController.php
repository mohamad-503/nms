<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Invoice;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index()
    {
        $revenue = Payment::sum('amount');
        $expenses = Expense::sum('amount');
        $invoices = Invoice::count();
        $paid = Invoice::where('status', 'paid')->count();

        $months = [];
        $labels = [];
        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $months[] = $d;
            $labels[] = $d->format('M');
        }
        $revByMonth = [];
        $expByMonth = [];
        foreach ($months as $m) {
            $revByMonth[] = Payment::whereYear('paid_date', $m->year)->whereMonth('paid_date', $m->month)->sum('amount');
            $expByMonth[] = Expense::whereYear('expense_date', $m->year)->whereMonth('expense_date', $m->month)->sum('amount');
        }

        return Inertia::render('Reports/Index', [
            'stats' => [
                'customers' => Customer::count(),
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $revenue - $expenses,
                'invoices' => $invoices,
                'paid' => $paid,
            ],
            'chartData' => [
                'labels' => $labels,
                'revenue' => $revByMonth,
                'expenses' => $expByMonth,
            ],
        ]);
    }
}
