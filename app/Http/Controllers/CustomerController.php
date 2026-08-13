<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Plan;
use App\Models\Area;
use App\Models\Tower;
use App\Models\City;
use App\Models\Invoice;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::with(['plan', 'city', 'area', 'tower']);

        if ($q = $request->get('search')) {
            $query->where(fn ($s) => $s->where('full_name', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")
                ->orWhere('pppoe_username', 'like', "%{$q}%")
                ->orWhere('national_id', 'like', "%{$q}%"));
        }
        if ($status = $request->get('status')) $query->where('status', $status);
        if ($city = $request->get('city_id')) $query->where('city_id', $city);
        if ($area = $request->get('area_id')) $query->where('area_id', $area);
        if ($plan = $request->get('plan_id')) $query->where('plan_id', $plan);

        $customers = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'plans' => Plan::where('is_active', true)->get(),
            'areas' => Area::all(),
            'cities' => City::all(),
            'filters' => $request->only(['search', 'status', 'city_id', 'area_id', 'plan_id']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Customers/Create', [
            'plans' => Plan::where('is_active', true)->get(),
            'areas' => Area::all(),
            'towers' => Tower::all(),
            'cities' => City::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('customers', 'public');
        }
        $customer = Customer::create($data);
        $this->log('create', 'customers', "إضافة مشترك: {$customer->full_name}");
        return redirect()->route('customers.show', $customer)->with('success', 'تم إضافة المشترك بنجاح');
    }

    public function show(Customer $customer)
    {
        $customer->load(['plan', 'city', 'area', 'tower', 'invoices', 'tickets']);
        return Inertia::render('Customers/Show', ['customer' => $customer]);
    }

    public function edit(Customer $customer)
    {
        return Inertia::render('Customers/Edit', [
            'customer' => $customer,
            'plans' => Plan::where('is_active', true)->get(),
            'areas' => Area::all(),
            'towers' => Tower::all(),
            'cities' => City::all(),
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $this->validateData($request, $customer->id);
        if ($request->hasFile('profile_photo')) {
            if ($customer->profile_photo) Storage::disk('public')->delete($customer->profile_photo);
            $data['profile_photo'] = $request->file('profile_photo')->store('customers', 'public');
        }
        $customer->update($data);
        $this->log('update', 'customers', "تعديل مشترك: {$customer->full_name}");
        return redirect()->route('customers.show', $customer)->with('success', 'تم تحديث المشترك');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->profile_photo) Storage::disk('public')->delete($customer->profile_photo);
        $name = $customer->full_name;
        $customer->delete();
        $this->log('delete', 'customers', "حذف مشترك: {$name}");
        return redirect()->route('customers.index')->with('success', 'تم حذف المشترك');
    }

    public function suspend(Customer $customer)
    {
        $customer->update(['status' => 'suspended']);
        $this->log('suspend', 'customers', "إيقاف: {$customer->full_name}");
        return back()->with('success', 'تم إيقاف المشترك');
    }

    public function activate(Customer $customer)
    {
        $customer->update(['status' => 'active']);
        $this->log('activate', 'customers', "تفعيل: {$customer->full_name}");
        return back()->with('success', 'تم تفعيل المشترك');
    }

    public function renew(Request $request, Customer $customer)
    {
        $days = $request->validate(['days' => 'required|integer|min:1'])['days'];
        $end = \Carbon\Carbon::parse($customer->subscription_end ?? now())->addDays($days);
        $customer->update(['subscription_end' => $end, 'status' => 'active']);
        $this->log('renew', 'customers', "تمديد {$customer->full_name} بـ {$days} يوم");
        return back()->with('success', "تم تمديد الاشتراك بـ {$days} يوم");
    }

    public function printContract(Customer $customer)
    {
        $customer->load(['plan', 'city', 'area']);
        $pdf = Pdf::loadView('pdf.contract', compact('customer'));
        return $pdf->stream("contract-{$customer->id}.pdf");
    }

    public function printInvoice(Customer $customer)
    {
        $invoice = Invoice::where('customer_id', $customer->id)->latest()->first();
        $pdf = Pdf::loadView('pdf.invoice', compact('customer', 'invoice'));
        return $pdf->stream("invoice-{$customer->id}.pdf");
    }

    private function validateData(Request $request, $id = null): array
    {
        return $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'national_id' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city_id' => 'nullable|exists:cities,id',
            'area_id' => 'nullable|exists:areas,id',
            'tower_id' => 'nullable|exists:towers,id',
            'pppoe_username' => "nullable|string|unique:customers,pppoe_username,{$id}|max:100",
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
            'status' => 'required|in:active,suspended,expired,inactive',
            'notes' => 'nullable|string',
        ]);
    }

    private function log(string $action, string $module, string $description): void
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => request()->ip(),
        ]);
    }
}
