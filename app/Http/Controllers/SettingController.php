<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return Inertia::render('Settings/Index', ['settings' => $settings]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array',
        ]);
        foreach ($data['settings'] as $key => $value) {
            Setting::set($key, $value);
        }
        return back()->with('success', 'تم حفظ الإعدادات');
    }
}
