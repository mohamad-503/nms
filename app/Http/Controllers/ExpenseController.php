<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\CashBoxTransaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::latest()->paginate(15);
        $total = Expense::sum('amount');
        return Inertia::render('Billing/Expenses', ['expenses' => $expenses, 'total' => $total]);
    }

    public function create()
    {
        return Inertia::render('Billing/CreateExpense');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'nullable|string|max:100',
            'amount' => 'required|numeric',
            'expense_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);
        $expense = Expense::create($data);
        CashBoxTransaction::create([
            'type' => 'out',
            'amount' => $data['amount'],
            'source' => 'expense',
            'transaction_date' => $data['expense_date'] ?? now(),
        ]);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'create', 'module' => 'expenses', 'description' => "مصروف: {$expense->category}", 'ip_address' => request()->ip()]);
        return redirect()->route('expenses.index')->with('success', 'تم تسجيل المصروف');
    }

    public function show(Expense $expense)
    {
        return Inertia::render('Billing/ShowExpense', ['expense' => $expense]);
    }

    public function edit(Expense $expense)
    {
        return Inertia::render('Billing/EditExpense', ['expense' => $expense]);
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'category' => 'nullable|string|max:100',
            'amount' => 'required|numeric',
            'expense_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);
        $expense->update($data);
        return redirect()->route('expenses.index')->with('success', 'تم تحديث المصروف');
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();
        return redirect()->route('expenses.index')->with('success', 'تم حذف المصروف');
    }
}
