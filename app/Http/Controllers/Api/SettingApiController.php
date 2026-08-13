<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingApiController extends Controller
{
    public function index() { return response()->json(Setting::all()->pluck('value','key')); }
    public function update(Request $request)
    {
        foreach ($request->validate(['settings'=>'required|array'])['settings'] as $k=>$v) Setting::set($k,$v);
        return response()->json(['ok'=>true]);
    }
}
