<?php
// app/Http/Controllers/SaleController.php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Branch;
use App\Models\NwowUnit;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'branch', 'items.product']);

        // Filter by branch (if user is not admin)
        if (!Auth::user()->isAdmin()) {
            $query->where('branch_id', Auth::user()->branch_id);
        } elseif ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('sale_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('sale_date', '<=', $request->date_to);
        }

        $sales = $query->latest()->paginate(15)->withQueryString();
        $branches = Branch::where('is_active', true)->get();

        $stats = [
            'total_sales' => $query->count(),
            'total_revenue' => $query->sum('final_amount'),
            'cash_sales' => $query->where('payment_method', 'cash')->count(),
            'installment_sales' => $query->where('payment_method', 'installment')->count(),
        ];

        return view('sales.index', compact('sales', 'branches', 'stats'));
    }

    public function create()
    {
        $userBranch = Auth::user()->branch_id;
        $products = Product::where('status', 'active')->get();
        $availableUnits = NwowUnit::with('product')
                                 ->where('branch_id', $userBranch)
                                 ->where('status', 'in_stock')
                                 ->get();

        return view('sales.create', compact('products', 'availableUnits', 'userBranch'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'payment_method' => 'required|in:cash,installment,home_credit,card,bank_transfer',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'sale_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.nwow_unit_id' => 'nullable|exists:nwow_units,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $userBranch = Auth::user()->branch_id;

        DB::transaction(function () use ($validated, $userBranch) {
            // Calculate totals
            $totalAmount = collect($validated['items'])->sum(function ($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $discount = $validated['discount'] ?? 0;
            $finalAmount = $totalAmount - $discount;

            // Create sale
            $sale = Sale::create([
                'sale_number' => Sale::generateSaleNumber(Auth::user()->branch->code),
                'user_id' => Auth::id(),
                'branch_id' => $userBranch,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'customer_address' => $validated['customer_address'],
                'payment_method' => $validated['payment_method'],
                'total_amount' => $totalAmount,
                'discount' => $discount,
                'final_amount' => $finalAmount,
                'notes' => $validated['notes'],
                'status' => 'completed',
                'sale_date' => $validated['sale_date'],
            ]);

            // Create sale items and update inventory
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['product_id']);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'nwow_unit_id' => $item['nwow_unit_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);

                if ($product->isUnit() && isset($item['nwow_unit_id'])) {
                    // Mark unit as sold
                    $unit = NwowUnit::findOrFail($item['nwow_unit_id']);
                    $unit->markAsSold();
                } else {
                    // Update parts/accessories inventory
                    $inventory = Inventory::where('branch_id', $userBranch)
                                         ->where('product_id', $product->id)
                                         ->first();
                    if ($inventory) {
                        $inventory->removeStock($item['quantity']);
                    }
                }
            }
        });

        return redirect()->route('sales.index')
            ->with('success', 'Sale completed successfully!');
    }

    public function show(Sale $sale)
    {
        if (!User::user()->canAccessBranch($sale->branch_id)) {
            abort(403, 'Access denied to this sale record.');
        }

        $sale->load(['user', 'branch', 'items.product', 'items.nwowUnit']);

        // Get NWOW export format if sale contains units
        $nwowExportData = [];
        foreach ($sale->items as $item) {
            if ($item->nwowUnit) {
                $unit = $item->nwowUnit;
                $nwowExportData[] = [
                    'date_sell' => $sale->sale_date->format('Y-m-d'),
                    'name_customer' => $sale->customer_name,
                    'number' => $sale->customer_phone,
                    'address' => $sale->customer_address,
                    'unit' => $item->product->name,
                    'color' => $unit->color,
                    'package' => $sale->payment_method,
                    'chassis' => $unit->chassis_no,
                    'motor' => $unit->motor_no,
                    'battery' => $unit->battery_no,
                    'controller' => $unit->controller_no,
                    'charger' => $unit->charger_no,
                    'remote' => $unit->remote_no,
                ];
            }
        }

        return view('sales.show', compact('sale', 'nwowExportData'));
    }

    public function edit(Sale $sale)
    {
        if ($sale->status !== 'completed') {
            return redirect()->route('sales.index')
                ->with('error', 'Only completed sales can be edited.');
        }

        if (!Auth::user()->canAccessBranch($sale->branch_id)) {
            abort(403, 'Access denied to this sale record.');
        }

        $products = Product::where('status', 'active')->get();
        return view('sales.edit', compact('sale', 'products'));
    }

    public function update(Request $request, Sale $sale)
    {
        if ($sale->status !== 'completed') {
            return redirect()->route('sales.index')
                ->with('error', 'Only completed sales can be updated.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_address' => 'nullable|string',
            'payment_method' => 'required|in:cash,installment,home_credit,card,bank_transfer',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Recalculate totals
        $totalAmount = $sale->items->sum('total_price');
        $discount = $validated['discount'] ?? 0;
        $finalAmount = $totalAmount - $discount;

        $sale->update(array_merge($validated, [
            'total_amount' => $totalAmount,
            'discount' => $discount,
            'final_amount' => $finalAmount,
        ]));

        return redirect()->route('sales.show', $sale)
            ->with('success', 'Sale updated successfully!');
    }

    public function cancel(Sale $sale)
    {
        if ($sale->status !== 'completed') {
            return redirect()->route('sales.index')
                ->with('error', 'Sale cannot be cancelled.');
        }

        DB::transaction(function () use ($sale) {
            // Restore inventory for each item
            foreach ($sale->items as $item) {
                if ($item->nwowUnit) {
                    // Return unit to stock
                    $item->nwowUnit->returnToStock();
                } else {
                    // Add back to inventory
                    $inventory = Inventory::firstOrCreate(
                        [
                            'branch_id' => $sale->branch_id,
                            'product_id' => $item->product_id,
                        ],
                        ['stock_quantity' => 0, 'min_stock_level' => 5]
                    );
                    $inventory->addStock($item->quantity);
                }
            }

            $sale->update(['status' => 'cancelled']);
        });

        return redirect()->route('sales.index')
            ->with('success', 'Sale cancelled and inventory restored!');
    }

    // API Methods
    public function getProductPrice(Product $product)
    {
        return response()->json([
            'price' => $product->price,
            'cost_price' => $product->cost_price,
        ]);
    }

    public function getBranchUnits(Branch $branch)
    {
        $units = NwowUnit::with('product')
                         ->where('branch_id', $branch->id)
                         ->where('status', 'in_stock')
                         ->get();

        return response()->json($units);
    }
}
