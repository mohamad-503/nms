<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('department');
        if ($q = $request->get('search')) $query->where('full_name','like',"%{$q}%");
        return response()->json($query->latest()->paginate(15));
    }
    public function store(Request $request) { return response()->json(Employee::create($request->validate(['full_name'=>'required|string','phone'=>'nullable|string','department_id'=>'nullable|exists:departments,id','position'=>'nullable|string','salary'=>'nullable|numeric','hire_date'=>'nullable|date','status'=>'in:active,inactive'])), 201); }
    public function show(Employee $employee) { return response()->json($employee->load('department')); }
    public function update(Request $request, Employee $employee) { $employee->update($request->validate(['full_name'=>'sometimes|string','phone'=>'nullable|string','department_id'=>'nullable|exists:departments,id','position'=>'nullable|string','salary'=>'nullable|numeric','status'=>'in:active,inactive'])); return response()->json($employee); }
    public function destroy(Employee $employee) { $employee->delete(); return response()->json(null, 204); }
}
