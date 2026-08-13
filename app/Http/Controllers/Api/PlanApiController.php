<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanApiController extends Controller
{
    public function index() { return response()->json(Plan::orderBy('price')->get()); }
    public function store(Request $request) { return response()->json(Plan::create($request->validate(['name'=>'required|string','price'=>'required|numeric','download_speed'=>'nullable|integer','upload_speed'=>'nullable|integer','burst'=>'nullable|string','validity'=>'nullable|integer','is_active'=>'boolean'])), 201); }
    public function show(Plan $plan) { return response()->json($plan); }
    public function update(Request $request, Plan $plan) { $plan->update($request->validate(['name'=>'sometimes|string','price'=>'sometimes|numeric','download_speed'=>'nullable|integer','upload_speed'=>'nullable|integer','burst'=>'nullable|string','validity'=>'nullable|integer','is_active'=>'boolean'])); return response()->json($plan); }
    public function destroy(Plan $plan) { $plan->delete(); return response()->json(null, 204); }
}
