<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryProduct;
use App\Models\StockMovement;
use Illuminate\Http\Request;

class InventoryProductApiController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryProduct::with(['category','supplier']);
        if ($q = $request->get('search')) $query->where('name','like',"%{$q}%")->orWhere('sku','like',"%{$q}%");
        return response()->json($query->latest()->paginate(15));
    }
    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|string','sku'=>'nullable|string|unique:inventory_products,sku','category_id'=>'nullable|exists:inventory_categories,id','supplier_id'=>'nullable|exists:inventory_suppliers,id','cost_price'=>'nullable|numeric','sale_price'=>'nullable|numeric','quantity'=>'nullable|integer','min_quantity'=>'nullable|integer','unit'=>'nullable|string']);
        $p = InventoryProduct::create($data);
        if (($data['quantity']??0)>0) StockMovement::create(['product_id'=>$p->id,'type'=>'in','quantity'=>$data['quantity'],'reference'=>'initial']);
        return response()->json($p, 201);
    }
    public function show(InventoryProduct $product) { return response()->json($product->load(['category','supplier','movements'])); }
    public function update(Request $request, InventoryProduct $product) { $product->update($request->validate(['name'=>'sometimes|string','sku'=>'nullable|string|unique:inventory_products,sku,'.$product->id,'quantity'=>'nullable|integer','min_quantity'=>'nullable|integer','sale_price'=>'nullable|numeric'])); return response()->json($product); }
    public function destroy(InventoryProduct $product) { $product->delete(); return response()->json(null, 204); }
}
