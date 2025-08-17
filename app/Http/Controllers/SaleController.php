<?php
// app/Http/Controllers/SaleController.php (Updated methods)

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\NwowUnit;
use App\Models\Inventory;

class SaleController extends Controller
{
    public function getBranchUnits($branchId)
{
    if (!Auth::user()->canAccessBranch($branchId)) {
        return response()->json(['error' => 'Access denied'], 403);
    }

    $units = NwowUnit::with('product')
                     ->where('branch_id', $branchId)
                     ->where('status', 'in_stock')
                     ->get();

    return response()->json($units);
}

// Update the store method in SaleController to handle units properly
public function store(Request $request)
{
    $validated = $request->validate([
        'customer_name' => 'required|string|max:255',
        'customer_email' => 'nullable|email',
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
    ]);

    DB::transaction(function () use ($validated, $request) {
        $userBranch = Auth::user()->branch_id;

        // Create the sale
        $sale = Sale::create([
            'sale_number' => Sale::generateSaleNumber(Auth::user()->branch->code),
            'user_id' => Auth::id(),
            'branch_id' => $userBranch,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'customer_address' => $validated['customer_address'],
            'payment_method' => $validated['payment_method'],
            'discount' => $validated['discount'] ?? 0,
            'notes' => $validated['notes'],
            'sale_date' => $validated['sale_date'],
            'total_amount' => 0,
            'final_amount' => 0,
            'status' => 'completed',
        ]);

        $totalAmount = 0;

        // Create sale items and update stock
        foreach ($validated['items'] as $item) {
            $product = Product::findOrFail($item['product_id']);
            $unitPrice = $product->price;
            $totalPrice = $unitPrice * $item['quantity'];
            $totalAmount += $totalPrice;

            if ($product->isUnit() && isset($item['nwow_unit_id'])) {
                // Handle unit sale
                $unit = NwowUnit::findOrFail($item['nwow_unit_id']);

                if ($unit->branch_id != $userBranch || $unit->status !== 'in_stock') {
                    throw new \Exception("Unit {$unit->chassis_no} is not available for sale.");
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'nwow_unit_id' => $unit->id,
                    'quantity' => 1,
                    'unit_price' => $unitPrice,
                    'total_price' => $unitPrice,
                ]);

                $unit->markAsSold();
            } else {
                // Handle parts/accessories sale
                $inventory = Inventory::where('branch_id', $userBranch)
                                     ->where('product_id', $product->id)
                                     ->first();

                if (!$inventory || $inventory->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}.");
                }

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                ]);

                $inventory->removeStock($item['quantity']);
            }
        }

        // Update sale totals
        $finalAmount = $totalAmount - ($validated['discount'] ?? 0);
        $sale->update([
            'total_amount' => $totalAmount,
            'final_amount' => $finalAmount,
        ]);
    });

    return redirect()->route('sales.index')
        ->with('success', 'Sale completed successfully!');
}
}