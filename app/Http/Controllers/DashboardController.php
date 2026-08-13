<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Plan;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\InventoryProduct;
use App\Models\SupportTicket;
use App\Models\ActivityLog;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'stats' => $this->stats(),
            'recentLogs' => ActivityLog::with('user')->latest()->limit(6)->get(),
            'revenueData' => $this->revenueData(),
            'customerStatusData' => $this->customerStatusData(),
        ]);
    }

    public function stats()
    {
        $today = now()->startOfDay();
        $monthStart = now()->startOfMonth();

        $customers = Customer::all();
        $payments = Payment::all();
        $expenses = Expense::all();

        $todayIncome = $payments->where('paid_date', '>=', $today)->sum('amount');
        $monthIncome = $payments->where('paid_date', '>=', $monthStart)->sum('amount');
        $monthExpenses = $expenses->where('expense_date', '>=', $monthStart)->sum('amount');

        return [
            'total' => $customers->count(),
            'active' => $customers->where('status', 'active')->count(),
            'expired' => $customers->where('status', 'expired')->count(),
            'suspended' => $customers->where('status', 'suspended')->count(),
            'todayIncome' => $todayIncome,
            'monthIncome' => $monthIncome,
            'expenses' => $monthExpenses,
            'profit' => $monthIncome - $monthExpenses,
            'lowStock' => InventoryProduct::whereColumn('quantity', '<=', 'min_quantity')->count(),
            'openTickets' => SupportTicket::whereIn('status', ['open', 'assigned', 'in_progress'])->count(),
        ];
    }

    private function revenueData()
    {
        $days = [];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = now()->subDays($i)->startOfDay();
            $days[] = $d;
            $labels[] = $d->format('D j');
        }
        $payments = Payment::all();
        $daily = [];
        foreach ($days as $d) {
            $daily[] = $payments->where('paid_date', fn ($v) => $v && $v->isSameDay($d))->sum('amount');
        }
        return [
            'labels' => $labels,
            'data' => $daily,
        ];
    }

    private function customerStatusData()
    {
        $customers = Customer::all();
        return [
            'labels' => ['نشط', 'موقوف', 'منتهي', 'غير نشط'],
            'data' => [
                $customers->where('status', 'active')->count(),
                $customers->where('status', 'suspended')->count(),
                $customers->where('status', 'expired')->count(),
                $customers->where('status', 'inactive')->count(),
            ],
        ];
    }
}
