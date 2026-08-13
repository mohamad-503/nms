<?php

namespace App\Http\Controllers;

use App\Models\Router;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RouterController extends Controller
{
    public function index()
    {
        return Inertia::render('Routers/Index', ['routers' => Router::latest()->get()]);
    }

    public function create()
    {
        return Inertia::render('Routers/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'ip' => 'required|string|max:100',
            'port' => 'nullable|integer',
            'username' => 'required|string|max:100',
            'password' => 'nullable|string|max:100',
            'use_ssl' => 'boolean',
        ]);
        $data['status'] = 'offline';
        Router::create($data);
        return redirect()->route('routers.index')->with('success', 'تم إضافة الجهاز');
    }

    public function show(Router $router)
    {
        return Inertia::render('Routers/Show', ['router' => $router]);
    }

    public function edit(Router $router)
    {
        return Inertia::render('Routers/Edit', ['router' => $router]);
    }

    public function update(Request $request, Router $router)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'ip' => 'required|string|max:100',
            'port' => 'nullable|integer',
            'username' => 'required|string|max:100',
            'password' => 'nullable|string|max:100',
            'use_ssl' => 'boolean',
        ]);
        $router->update($data);
        return redirect()->route('routers.index')->with('success', 'تم تحديث الجهاز');
    }

    public function destroy(Router $router)
    {
        $router->delete();
        return redirect()->route('routers.index')->with('success', 'تم حذف الجهاز');
    }

    public function test(Router $router)
    {
        $ok = !empty($router->ip) && !empty($router->username);
        $router->update([
            'status' => $ok ? 'online' : 'error',
            'last_checked' => now(),
        ]);
        return back()->with('success', $ok ? 'الاتصال ناجح' : 'فشل الاتصال');
    }
}
