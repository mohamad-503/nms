<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\CashBoxTransaction;
use Illuminate\Http\Request;

class ExpenseApiController extends Controller
{
    public function index() { return response()->json(Expense::latest()->paginate(15)); }
    public function store(Request $request) {
        $data = $request->validate(['category'=>'nullable|string','amount'=>'required|numeric','expense_date'=>'nullable|date','description'=>'nullable|string']);
        $e = Expense::create($data);
        CashBoxTransaction::create(['type'=>'out','amount'=>$data['amount'],'source'=>'expense','transaction_date'=>$data['expense_date']??now()]);
        return response()->json($e, 201);
    }
    public function show(Expense $expense) { return response()->json($expense); }
    public function update(Request $request, Expense $expense) { $expense->update($request->validate(['category'=>'nullable|string','amount'=>'sometimes|numeric','expense_date'=>'nullable|date','description'=>'nullable|string'])); return response()->json($expense); }
    public function destroy(Expense $expense) { $expense->delete(); return response()->json(null, 204); }
}
