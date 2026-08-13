<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Inertia\Inertia;

class PlanController extends Controller
{
    public function index()
    {
        return Inertia::render('Plans/Index', ['plans' => Plan::orderBy('price')->get()]);
    }

    public function create()
    {
        return Inertia::render('Plans/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'download_speed' => 'nullable|integer',
            'upload_speed' => 'nullable|integer',
            'burst' => 'nullable|string',
            'validity' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);
        $plan = Plan::create($data);
        ActivityLog::create(['user_id' => auth()->id(), 'action' => 'create', 'module' => 'plans', 'description' => "باقة: {$plan->name}", 'ip_address' => request()->ip()]);
        return redirect()->route('plans.index')->with('success', 'تم إضافة الباقة');
    }

    public function show(Plan $plan)
    {
        return Inertia::render('Plans/Show', ['plan' => $plan]);
    }

    public function edit(Plan $plan)
    {
        return Inertia::render('Plans/Edit', ['plan' => $plan]);
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'download_speed' => 'nullable|integer',
            'upload_speed' => 'nullable|integer',
            'burst' => 'nullable|string',
            'validity' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);
        $plan->update($data);
        return redirect()->route('plans.index')->with('success', 'تم تحديث الباقة');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('plans.index')->with('success', 'تم حذف الباقة');
    }
}
