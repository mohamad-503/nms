<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Department;
use App\Models\Attendance;
use App\Models\Leave;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('department');
        if ($q = $request->get('search')) {
            $query->where('full_name', 'like', "%{$q}%")->orWhere('phone', 'like', "%{$q}%");
        }
        return Inertia::render('Employees/Index', [
            'employees' => $query->latest()->paginate(15)->withQueryString(),
            'departments' => Department::all(),
            'attendance' => Attendance::with('employee')->latest()->limit(20)->get(),
            'leaves' => Leave::with('employee')->latest()->limit(20)->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Employees/Create', ['departments' => Department::all()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:100',
            'salary' => 'nullable|numeric',
            'hire_date' => 'nullable|date',
            'status' => 'in:active,inactive',
        ]);
        Employee::create($data);
        return redirect()->route('employees.index')->with('success', 'تم إضافة الموظف');
    }

    public function show(Employee $employee)
    {
        return Inertia::render('Employees/Show', ['employee' => $employee->load('department', 'attendance', 'leaves')]);
    }

    public function edit(Employee $employee)
    {
        return Inertia::render('Employees/Edit', ['employee' => $employee, 'departments' => Department::all()]);
    }

    public function update(Request $request, Employee $employee)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'department_id' => 'nullable|exists:departments,id',
            'position' => 'nullable|string|max:100',
            'salary' => 'nullable|numeric',
            'hire_date' => 'nullable|date',
            'status' => 'in:active,inactive',
        ]);
        $employee->update($data);
        return redirect()->route('employees.index')->with('success', 'تم تحديث الموظف');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'تم حذف الموظف');
    }
}
