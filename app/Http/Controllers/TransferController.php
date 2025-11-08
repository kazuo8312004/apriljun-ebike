<?php
// app/Http/Controllers/TransferController.php

namespace App\Http\Controllers;

use App\Models\Transfer;
use App\Models\TransferItem;
use App\Models\Branch;
use App\Models\Product;
use App\Models\NwowUnit;
use App\Models\Inventory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Photo;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $query = Transfer::with(['fromBranch', 'toBranch', 'user', 'items']);

        // Filter by branch access
        if (!Auth::user()->isAdmin()) {
            $userBranch = Auth::user()->getSelectedBranchId();
            $query->where(function ($q) use ($userBranch) {
                $q->where('from_branch_id', $userBranch)
                  ->orWhere('to_branch_id', $userBranch);
            });
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('transfer_number', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by branch
        if ($request->filled('branch_id')) {
            $branchId = $request->branch_id;
            $query->where(function ($q) use ($branchId) {
                $q->where('from_branch_id', $branchId)
                  ->orWhere('to_branch_id', $branchId);
            });
        }

        $transfers = $query->latest()->paginate(15)->withQueryString();
        $branches = Branch::where('is_active', true)->get();

        return view('transfers.index', compact('transfers', 'branches'));
    }

    public function create()
    {
        $branches = Branch::where('is_active', true)->get();
        $userBranch = Auth::user()->getSelectedBranchId();

        return view('transfers.create', compact('branches', 'userBranch'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.nwow_unit_id' => 'nullable|exists:nwow_units,id',
            'items.*.quantity' => 'required|integer|min:1',
            'photos' => 'nullable|array',
            'photos.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::transaction(function () use ($validated) {
            // Create transfer
            $transfer = Transfer::create([
                'transfer_number' => Transfer::generateTransferNumber(),
                'from_branch_id' => $validated['from_branch_id'],
                'to_branch_id' => $validated['to_branch_id'],
                'user_id' => Auth::id(),
                'notes' => $validated['notes'],
                'status' => 'pending',
            ]);

            // Create transfer items and check availability
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                if ($product->isUnit() && isset($item['nwow_unit_id'])) {
                    // Handle unit transfer
                    $unit = NwowUnit::findOrFail($item['nwow_unit_id']);

                    if ($unit->branch_id != $validated['from_branch_id'] || $unit->status !== 'in_stock') {
                        throw new \Exception("Unit {$unit->chassis_no} is not available for transfer.");
                    }

                    TransferItem::create([
                        'transfer_id' => $transfer->id,
                        'product_id' => $product->id,
                        'nwow_unit_id' => $unit->id,
                        'quantity' => 1,
                        'notes' => $item['notes'] ?? null,
                    ]);
                } else {
                    // Handle parts/accessories transfer
                    $inventory = Inventory::where('branch_id', $validated['from_branch_id'])
                                         ->where('product_id', $product->id)
                                         ->first();

                    if (!$inventory || $inventory->stock_quantity < $item['quantity']) {
                        throw new \Exception("Insufficient stock for {$product->name} in source branch.");
                    }

                    TransferItem::create([
                        'transfer_id' => $transfer->id,
                        'product_id' => $product->id,
                        'quantity' => $item['quantity'],
                        'notes' => $item['notes'] ?? null,
                    ]);
                }
            }

            // Handle photo uploads
            if (isset($validated['photos']) && is_array($validated['photos'])) {
                foreach ($validated['photos'] as $photo) {
                    if ($photo) {
                        $path = $photo->store('transfers', 'public');
                        Photo::create([
                            'path' => $path,
                            'filename' => $photo->getClientOriginalName(),
                            'mime_type' => $photo->getMimeType(),
                            'size' => $photo->getSize(),
                            'model_type' => Transfer::class,
                            'model_id' => $transfer->id,
                            'category' => 'transfer',
                            'description' => 'Transfer photo',
                        ]);
                    }
                }
            }
        });

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer created successfully!');
    }

    public function show(Transfer $transfer)
    {
        $transfer->load(['fromBranch', 'toBranch', 'user', 'receivedByUser', 'items.product', 'items.nwowUnit']);
        return view('transfers.show', compact('transfer'));
    }

    public function approve(Transfer $transfer)
    {
        if ($transfer->status !== 'pending') {
            return redirect()->route('transfers.index')
                ->with('error', 'Only pending transfers can be approved.');
        }

        DB::transaction(function () use ($transfer) {
            foreach ($transfer->items as $item) {
                if ($item->nwow_unit_id) {
                    // Mark unit as transferred
                    $item->nwowUnit->markAsTransferred();
                } else {
                    // Remove from source branch inventory
                    $sourceInventory = Inventory::where('branch_id', $transfer->from_branch_id)
                                               ->where('product_id', $item->product_id)
                                               ->first();
                    if ($sourceInventory) {
                        $sourceInventory->removeStock($item->quantity);
                    }
                }
            }

            $transfer->update([
                'status' => 'in_transit',
                'transferred_at' => now(),
            ]);
        });

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer approved and items are now in transit!');
    }

    public function receive(Transfer $transfer)
    {
        if ($transfer->status !== 'in_transit') {
            return redirect()->route('transfers.index')
                ->with('error', 'Only transfers in transit can be received.');
        }

        if (!Auth::user()->canAccessBranch($transfer->to_branch_id)) {
            return redirect()->route('transfers.index')
                ->with('error', 'You can only receive transfers for your branch.');
        }

        DB::transaction(function () use ($transfer) {
            foreach ($transfer->items as $item) {
                if ($item->nwow_unit_id) {
                    // Update unit branch and status
                    $item->nwowUnit->update([
                        'branch_id' => $transfer->to_branch_id,
                        'status' => 'in_stock',
                    ]);
                } else {
                    // Add to destination branch inventory
                    $destInventory = Inventory::firstOrCreate(
                        [
                            'branch_id' => $transfer->to_branch_id,
                            'product_id' => $item->product_id,
                        ],
                        ['stock_quantity' => 0, 'min_stock_level' => 5]
                    );
                    $destInventory->addStock($item->quantity);
                }
            }

            $transfer->update([
                'status' => 'completed',
                'received_at' => now(),
                'received_by' => Auth::id(),
            ]);
        });

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer received successfully!');
    }

    public function cancel(Transfer $transfer)
    {
        if (!in_array($transfer->status, ['pending', 'in_transit'])) {
            return redirect()->route('transfers.index')
                ->with('error', 'Transfer cannot be cancelled at this stage.');
        }

        DB::transaction(function () use ($transfer) {
            if ($transfer->status === 'in_transit') {
                // Restore items if already transferred
                foreach ($transfer->items as $item) {
                    if ($item->nwow_unit_id) {
                        $item->nwowUnit->update([
                            'branch_id' => $transfer->from_branch_id,
                            'status' => 'in_stock',
                        ]);
                    } else {
                        $sourceInventory = Inventory::where('branch_id', $transfer->from_branch_id)
                                                   ->where('product_id', $item->product_id)
                                                   ->first();
                        if ($sourceInventory) {
                            $sourceInventory->addStock($item->quantity);
                        }
                    }
                }
            }

            $transfer->update(['status' => 'cancelled']);
        });

        return redirect()->route('transfers.index')
            ->with('success', 'Transfer cancelled successfully!');
    }

    // API endpoint for getting available items in branch
    public function getBranchItems($branchId)
    {
        $units = NwowUnit::with('product')
                         ->where('branch_id', $branchId)
                         ->where('status', 'in_stock')
                         ->get();

        $parts = Inventory::with('product')
                         ->where('branch_id', $branchId)
                         ->where('stock_quantity', '>', 0)
                         ->get();

        return response()->json([
            'units' => $units,
            'parts' => $parts,
        ]);
    }
}
