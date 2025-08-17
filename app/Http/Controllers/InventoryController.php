<?php
// app/Http/Controllers/InventoryController.php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with(['product', 'branch']);

        // Filter by branch (if user is not admin)
        if (!Auth::user()->isAdmin()) {
            $query->where('branch_id', Auth::user()->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by product type
        if ($request->filled('type')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('type', $request->type);
            });
        }

        // Filter low stock
        if ($request->filled('low_stock')) {
            $query->whereRaw('stock_quantity <= min_stock_level');
        }

        $inventories = $query->paginate(15)->withQueryString();
        $branches = Branch::where('is_active', true)->get();

        return view('inventory.index', compact('inventories', 'branches'));
    }

    public function byBranch(Branch $branch)
    {
        if (!Auth::user()->canAccessBranch($branch->id)) {
            abort(403, 'Access denied to this branch.');
        }

        $inventories = Inventory::with('product')
                               ->where('branch_id', $branch->id)
                               ->paginate(15);

        return view('inventory.branch', compact('branch', 'inventories'));
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'adjustment' => 'required|integer',
            'reason' => 'required|string|max:255',
        ]);

        $inventory = Inventory::findOrFail($validated['inventory_id']);

        if (!Auth::user()->canAccessBranch($inventory->branch_id)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $newQuantity = $inventory->stock_quantity + $validated['adjustment'];

        if ($newQuantity < 0) {
            return response()->json(['error' => 'Cannot reduce stock below zero'], 400);
        }

        $inventory->update(['stock_quantity' => $newQuantity]);

        return response()->json([
            'message' => 'Stock adjusted successfully',
            'new_quantity' => $newQuantity
        ]);
    }
}
