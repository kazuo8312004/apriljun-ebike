<?php
// app/Http/Controllers/InventoryController.php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Events\InventoryAdded;


class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Inventory::with(['product', 'branch']);

        // Filter by branch (if user is not admin)
        if (!Auth::user()->isAdmin()) {
            $query->where('branch_id', Auth::user()->getSelectedBranchId());
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

        // Filter by low stock
        if ($request->filled('low_stock')) {
            $query->whereRaw('stock_quantity <= min_stock_level');
        }

        // Filter by product type
        if ($request->filled('type')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('type', $request->type);
            });
        }

        $inventories = $query->paginate(15)->withQueryString();
        $branches = Branch::where('is_active', true)->get();

        $stats = [
            'total_items' => $query->count(),
            'low_stock_items' => Inventory::whereRaw('stock_quantity <= min_stock_level')->count(),
            'out_of_stock' => Inventory::where('stock_quantity', 0)->count(),
        ];

        return view('inventory.index', compact('inventories', 'branches', 'stats'));
    }

    public function byBranch(Branch $branch)
    {
        if (!Auth::user()->canAccessBranch($branch->id)) {
            abort(403, 'Access denied to this branch.');
        }

        $inventories = Inventory::with(['product'])
                               ->where('branch_id', $branch->id)
                               ->paginate(15);

        return view('inventory.branch', compact('inventories', 'branch'));
    }

    public function adjust(Request $request)
    {
        $validated = $request->validate([
            'inventory_id' => 'required|exists:inventories,id',
            'adjustment_type' => 'required|in:add,subtract,set',
            'quantity' => 'required|integer|min:0',
            'reason' => 'required|string|max:255',
        ]);

        $inventory = Inventory::findOrFail($validated['inventory_id']);

        if (!Auth::user()->canAccessBranch($inventory->branch_id)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        DB::transaction(function () use ($inventory, $validated) {
            $oldQuantity = $inventory->stock_quantity;

            switch ($validated['adjustment_type']) {
                case 'add':
                    $inventory->addStock($validated['quantity']);
                    break;
                case 'subtract':
                    if ($inventory->stock_quantity >= $validated['quantity']) {
                        $inventory->removeStock($validated['quantity']);
                    } else {
                        throw new \Exception('Insufficient stock to subtract.');
                    }
                    break;
                case 'set':
                    $inventory->update(['stock_quantity' => $validated['quantity']]);
                    break;
            }

            // Log the adjustment and fire event if stock was added
            Log::info('Inventory adjustment', [
                'user_id' => Auth::id(),
                'inventory_id' => $inventory->id,
                'product' => $inventory->product->name,
                'branch' => $inventory->branch->name,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $inventory->fresh()->stock_quantity,
                'adjustment_type' => $validated['adjustment_type'],
                'quantity' => $validated['quantity'],
                'reason' => $validated['reason'],
            ]);

            // Fire event if stock was added
            if ($validated['adjustment_type'] === 'add') {
                event(new InventoryAdded($inventory, Auth::user(), $validated['quantity'], $validated['reason']));
            }
        });

        return response()->json(['success' => 'Inventory adjusted successfully']);
    }
}
