<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\CashBoxTransaction;
use Illuminate\Http\Request;

class InvoiceApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('customer');
        if ($q = $request->get('search')) $query->where('invoice_number', 'like', "%{$q}%");
        return response()->json($query->latest()->paginate(15));
    }
    public function store(Request $request)
    {
        $data = $request->validate(['customer_id'=>'required|exists:customers,id','plan_id'=>'nullable|exists:plans,id','amount'=>'nullable|numeric','due_date'=>'nullable|date']);
        $amount = $data['amount'] ?? 0;
        $invoice = Invoice::create(['invoice_number'=>'INV-'.str_pad(Invoice::count()+100001,6,'0'),'customer_id'=>$data['customer_id'],'plan_id'=>$data['plan_id']??null,'amount'=>$amount,'total'=>$amount,'status'=>'unpaid','issued_date'=>now(),'due_date'=>$data['due_date']??null]);
        return response()->json($invoice, 201);
    }
    public function show(Invoice $invoice) { return response()->json($invoice->load('customer','plan','payments')); }
    public function update(Request $request, Invoice $invoice) { $invoice->update($request->validate(['status'=>'in:paid,unpaid,partial,cancelled','due_date'=>'nullable|date'])); return response()->json($invoice); }
    public function destroy(Invoice $invoice) { $invoice->delete(); return response()->json(null, 204); }
    public function markPaid(Invoice $invoice)
    {
        $invoice->update(['status'=>'paid']);
        Payment::create(['invoice_id'=>$invoice->id,'customer_id'=>$invoice->customer_id,'amount'=>$invoice->total,'method'=>'cash','paid_date'=>now()]);
        CashBoxTransaction::create(['type'=>'in','amount'=>$invoice->total,'source'=>'invoice','reference'=>$invoice->invoice_number,'transaction_date'=>now()]);
        return response()->json($invoice);
    }
}
