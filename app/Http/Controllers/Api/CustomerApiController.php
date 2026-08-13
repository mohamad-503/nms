<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with(['plan', 'city', 'area', 'tower']);
        if ($q = $request->get('search')) {
            $query->where(fn ($s) => $s->where('full_name', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")->orWhere('pppoe_username', 'like', "%{$q}%"));
        }
        if ($status = $request->get('status')) $query->where('status', $status);
        return response()->json($query->latest()->paginate($request->get('per_page', 15)));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
            'area_id' => 'nullable|exists:areas,id',
            'tower_id' => 'nullable|exists:towers,id',
            'pppoe_username' => 'nullable|string|unique:customers,pppoe_username|max:100',
            'pppoe_password' => 'nullable|string|max:100',
            'plan_id' => 'nullable|exists:plans,id',
            'download_speed' => 'nullable|integer',
            'upload_speed' => 'nullable|integer',
            'static_ip' => 'nullable|string|max:50',
            'mac_address' => 'nullable|string|max:50',
            'installation_date' => 'nullable|date',
            'subscription_start' => 'nullable|date',
            'subscription_end' => 'nullable|date',
            'monthly_price' => 'nullable|numeric',
            'status' => 'in:active,suspended,expired,inactive',
            'notes' => 'nullable|string',
        ]);
        return response()->json(Customer::create($data), 201);
    }

    public function show(Customer $customer)
    {
        return response()->json($customer->load(['plan', 'city', 'area', 'tower', 'invoices', 'tickets']));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'full_name' => 'sometimes|string|max:255',
            'phone' => 'nullable|string|max:50',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
            'area_id' => 'nullable|exists:areas,id',
            'tower_id' => 'nullable|exists:towers,id',
            'pppoe_username' => "nullable|string|unique:customers,pppoe_username,{$customer->id}|max:100",
            'pppoe_password' => 'nullable|string|max:100',
            'plan_id' => 'nullable|exists:plans,id',
            'download_speed' => 'nullable|integer',
            'upload_speed' => 'nullable|integer',
            'static_ip' => 'nullable|string|max:50',
            'mac_address' => 'nullable|string|max:50',
            'installation_date' => 'nullable|date',
            'subscription_start' => 'nullable|date',
            'subscription_end' => 'nullable|date',
            'monthly_price' => 'nullable|numeric',
            'status' => 'in:active,suspended,expired,inactive',
            'notes' => 'nullable|string',
        ]);
        $customer->update($data);
        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(null, 204);
    }

    public function suspend(Customer $customer)
    {
        $customer->update(['status' => 'suspended']);
        return response()->json($customer);
    }

    public function activate(Customer $customer)
    {
        $customer->update(['status' => 'active']);
        return response()->json($customer);
    }

    public function renew(Request $request, Customer $customer)
    {
        $days = $request->validate(['days' => 'required|integer|min:1'])['days'];
        $end = \Carbon\Carbon::parse($customer->subscription_end ?? now())->addDays($days);
        $customer->update(['subscription_end' => $end, 'status' => 'active']);
        return response()->json($customer);
    }
}
