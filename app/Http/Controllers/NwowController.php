<?php
// app/Http/Controllers/NwowController.php

namespace App\Http\Controllers;

use App\Models\NwowUnit;
use App\Models\Product;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NwowController extends Controller
{
    public function index(Request $request)
    {
        $query = NwowUnit::with(['product', 'branch']);

        // Filter by branch (if user is not admin)
        if (Auth::check() && !Auth::user()->isAdmin()) {
            $query->where('branch_id', Auth::user()->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('chassis_no', 'like', "%{$search}%")
                  ->orWhere('motor_no', 'like', "%{$search}%")
                  ->orWhere('battery_no', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($productQuery) use ($search) {
                      $productQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by product
        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        $units = $query->latest()->paginate(15)->withQueryString();
        $branches = Branch::where('is_active', true)->get();
        $products = Product::where('type', 'unit')->where('status', 'active')->get();

        return view('nwow.index', compact('units', 'branches', 'products'));
    }

    public function create()
    {
        $products = Product::where('type', 'unit')->where('status', 'active')->get();
        $branches = Branch::where('is_active', true)->get();

        return view('nwow.create', compact('products', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'branch_id' => 'required|exists:branches,id',
            'chassis_no' => 'required|string|unique:nwow_units',
            'motor_no' => 'nullable|string',
            'battery_no' => 'nullable|string',
            'controller_no' => 'nullable|string',
            'charger_no' => 'nullable|string',
            'remote_no' => 'nullable|string',
            'color' => 'nullable|string',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
        ]);

        NwowUnit::create($validated);

        return redirect()->route('nwow.index')
            ->with('success', 'Unit added to NWOW successfully!');
    }

    public function show(NwowUnit $nwow)
    {
        $nwow->load(['product', 'branch', 'saleItems.sale', 'loans']);
        return view('nwow.show', compact('nwow'));
    }

    public function edit(NwowUnit $nwow)
    {
        if ($nwow->status !== 'in_stock') {
            return redirect()->route('nwow.index')
                ->with('error', 'Only units in stock can be edited.');
        }

        $products = Product::where('type', 'unit')->where('status', 'active')->get();
        $branches = Branch::where('is_active', true)->get();

        return view('nwow.edit', compact('nwow', 'products', 'branches'));
    }

    public function update(Request $request, NwowUnit $nwow)
    {
        if ($nwow->status !== 'in_stock') {
            return redirect()->route('nwow.index')
                ->with('error', 'Only units in stock can be updated.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'branch_id' => 'required|exists:branches,id',
            'chassis_no' => 'required|string|unique:nwow_units,chassis_no,' . $nwow->id,
            'motor_no' => 'nullable|string',
            'battery_no' => 'nullable|string',
            'controller_no' => 'nullable|string',
            'charger_no' => 'nullable|string',
            'remote_no' => 'nullable|string',
            'color' => 'nullable|string',
            'purchase_date' => 'required|date',
            'purchase_price' => 'required|numeric|min:0',
        ]);

        $nwow->update($validated);

        return redirect()->route('nwow.index')
            ->with('success', 'Unit updated successfully!');
    }

    public function destroy(NwowUnit $nwow)
    {
        if ($nwow->status !== 'in_stock') {
            return redirect()->route('nwow.index')
                ->with('error', 'Only units in stock can be deleted.');
        }

        $nwow->delete();

        return redirect()->route('nwow.index')
            ->with('success', 'Unit deleted successfully!');
    }
}
