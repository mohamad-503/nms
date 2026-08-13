<?php

namespace App\Http\Controllers;

use App\Models\InventoryProduct;
use App\Models\InventoryCategory;
use App\Models\InventorySupplier;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryProductController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryProduct::with(['category', 'supplier']);
        if ($q = $request->get('search')) {
            $query->where('name', 'like', "%{$q}%")->orWhere('sku', 'like', "%{$q}%");
        }
        $products = $query->latest()->paginate(15)->withQueryString();
        $lowStock = InventoryProduct::whereColumn('quantity', '<=', 'min_quantity')->count();
        return Inertia::render('Inventory/Index', [
            'products' => $products,
            'categories' => InventoryCategory::all(),
            'suppliers' => InventorySupplier::all(),
            'movements' => StockMovement::with('product')->latest()->limit(20)->get(),
            'lowStock' => $lowStock,
        ]);
    }

    public function create()
    {
        return Inertia::render('Inventory/Create', [
            'categories' => InventoryCategory::all(),
            'suppliers' => InventorySupplier::all(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:inventory_products,sku|max:100',
            'category_id' => 'nullable|exists:inventory_categories,id',
            'supplier_id' => 'nullable|exists:inventory_suppliers,id',
            'cost_price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'quantity' => 'nullable|integer',
            'min_quantity' => 'nullable|integer',
            'unit' => 'nullable|string|max:50',
        ]);
        $product = InventoryProduct::create($data);
        if (($data['quantity'] ?? 0) > 0) {
            StockMovement::create(['product_id' => $product->id, 'type' => 'in', 'quantity' => $data['quantity'], 'reference' => 'initial']);
        }
        return redirect()->route('inventory.index')->with('success', 'تم إضافة المنتج');
    }

    public function show(InventoryProduct $product)
    {
        return Inertia::render('Inventory/Show', ['product' => $product->load(['category', 'supplier', 'movements', 'serials'])]);
    }

    public function edit(InventoryProduct $product)
    {
        return Inertia::render('Inventory/Edit', [
            'product' => $product,
            'categories' => InventoryCategory::all(),
            'suppliers' => InventorySupplier::all(),
        ]);
    }

    public function update(Request $request, InventoryProduct $product)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => "nullable|string|unique:inventory_products,sku,{$product->id}|max:100",
            'category_id' => 'nullable|exists:inventory_categories,id',
            'supplier_id' => 'nullable|exists:inventory_suppliers,id',
            'cost_price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'quantity' => 'nullable|integer',
            'min_quantity' => 'nullable|integer',
            'unit' => 'nullable|string|max:50',
        ]);
        $product->update($data);
        return redirect()->route('inventory.index')->with('success', 'تم تحديث المنتج');
    }

    public function destroy(InventoryProduct $product)
    {
        $product->delete();
        return redirect()->route('inventory.index')->with('success', 'تم حذف المنتج');
    }
}
