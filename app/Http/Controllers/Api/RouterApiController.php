<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Router;
use Illuminate\Http\Request;

class RouterApiController extends Controller
{
    public function index() { return response()->json(Router::latest()->get()); }
    public function store(Request $request) { return response()->json(Router::create($request->validate(['name'=>'required|string','ip'=>'required|string','port'=>'nullable|integer','username'=>'required|string','password'=>'nullable|string','use_ssl'=>'boolean'])+['status'=>'offline']), 201); }
    public function show(Router $router) { return response()->json($router); }
    public function update(Request $request, Router $router) { $router->update($request->validate(['name'=>'sometimes|string','ip'=>'sometimes|string','port'=>'nullable|integer','username'=>'sometimes|string','password'=>'nullable|string','use_ssl'=>'boolean'])); return response()->json($router); }
    public function destroy(Router $router) { $router->delete(); return response()->json(null, 204); }
    public function test(Router $router) { $ok = !empty($router->ip)&&!empty($router->username); $router->update(['status'=>$ok?'online':'error','last_checked'=>now()]); return response()->json(['ok'=>$ok,'status'=>$router->status]); }
}
