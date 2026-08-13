<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Plan;
use App\Models\Payment;
use App\Models\CashBoxTransaction;
use App\Models\Expense;
use App\Models\Debt;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('customer');
        if ($q = $request->get('search')) {
            $query->where('invoice_number', 'like', "%{$q}%")
                ->orWhereHas('customer', fn ($c) => $c->where('full_name', 'like', "%{$q}%"));
        }
        return Inertia::render('Billing/Invoices', [
            'invoices' => $query->latest()->paginate(15)->withQueryString(),
            'customers' => Customer::all(['id', 'full_name']),
            'plans' => Plan::where('is_active', true)->get(['id', 'name', 'price']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Billing/CreateInvoice', [
            'customers' => Customer::all(['id', 'full_name']),
            'plans' => Plan::where('is_active', true)->get(['id', 'name', 'price']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'plan_id' => 'nullable|exists:plans,id',
            'amount' => 'nullable|numeric',
            'due_date' => 'nullable|date',
        ]);
        $plan = Plan::find($data['plan_id'] ?? null);
        $amount = $plan ? $plan->price : ($data['amount'] ?? 0);
        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . str_pad(Invoice::count() + 100001, 6, '0'),
            'customer_id' => $data['customer_id'],
            'plan_id' => $data['plan_id'] ?? null,
            'amount' => $amount,
            'total' => $amount,
            'status' => 'unpaid',
            'issued_date' => now(),
            'due_date' => $data['due_date'] ?? null,
        ]);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'create', 'module' => 'invoices', 'description' => "فاتورة {$invoice->invoice_number}", 'ip_address' => request()->ip()]);
        return redirect()->route('invoices.index')->with('success', 'تم إنشاء الفاتورة');
    }

    public function show(Invoice $invoice)
    {
        return Inertia::render('Billing/ShowInvoice', ['invoice' => $invoice->load('customer', 'plan', 'payments')]);
    }

    public function edit(Invoice $invoice)
    {
        return Inertia::render('Billing/EditInvoice', [
            'invoice' => $invoice,
            'customers' => Customer::all(['id', 'full_name']),
            'plans' => Plan::where('is_active', true)->get(['id', 'name', 'price']),
        ]);
    }

    public function update(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'plan_id' => 'nullable|exists:plans,id',
            'amount' => 'nullable|numeric',
            'status' => 'in:paid,unpaid,partial,cancelled',
            'due_date' => 'nullable|date',
        ]);
        $invoice->update($data);
        return redirect()->route('invoices.index')->with('success', 'تم تحديث الفاتورة');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();
        return redirect()->route('invoices.index')->with('success', 'تم حذف الفاتورة');
    }

    public function markPaid(Invoice $invoice)
    {
        $invoice->update(['status' => 'paid']);
        Payment::create([
            'invoice_id' => $invoice->id,
            'customer_id' => $invoice->customer_id,
            'amount' => $invoice->total,
            'method' => 'cash',
            'paid_date' => now(),
        ]);
        CashBoxTransaction::create([
            'type' => 'in',
            'amount' => $invoice->total,
            'source' => 'invoice',
            'reference' => $invoice->invoice_number,
            'transaction_date' => now(),
        ]);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'payment', 'module' => 'invoices', 'description' => "تحصيل {$invoice->invoice_number}", 'ip_address' => request()->ip()]);
        return back()->with('success', 'تم تحصيل الفاتورة');
    }
}
