<?php
// app/Http/Controllers/DashboardController.php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Branch;
use App\Models\NwowUnit;
use App\Models\Loan;
use App\Models\Transfer;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $userBranch = $user->branch_id;

        if ($user->isAdmin()) {
            // Admin dashboard - system-wide stats
            $stats = [
                'total_branches' => Branch::where('is_active', true)->count(),
                'total_units' => NwowUnit::where('status', 'in_stock')->count(),
                'total_sales_today' => Sale::whereDate('sale_date', today())->count(),
                'total_revenue_today' => Sale::whereDate('sale_date', today())->sum('final_amount'),
                'active_loans' => Loan::where('status', 'active')->count(),
                'pending_transfers' => Transfer::where('status', 'pending')->count(),
                'overdue_loans' => Loan::where('status', 'active')
                                      ->where('expected_return_date', '<', now())
                                      ->count(),
            ];

            $branchStats = Branch::withCount([
                'nwowUnits as units_in_stock' => function ($query) {
                    $query->where('status', 'in_stock');
                },
                'sales as sales_today' => function ($query) {
                    $query->whereDate('sale_date', today());
                },
                'loans as active_loans' => function ($query) {
                    $query->where('status', 'active');
                }
            ])->where('is_active', true)->get();

            $recentTransfers = Transfer::with(['fromBranch', 'toBranch'])
                                     ->latest()
                                     ->take(5)
                                     ->get();

        } else {
            // Branch-specific dashboard
            $stats = [
                'units_in_stock' => NwowUnit::where('branch_id', $userBranch)
                                           ->where('status', 'in_stock')
                                           ->count(),
                'sales_today' => Sale::where('branch_id', $userBranch)
                                    ->whereDate('sale_date', today())
                                    ->count(),
                'revenue_today' => Sale::where('branch_id', $userBranch)
                                      ->whereDate('sale_date', today())
                                      ->sum('final_amount'),
                'active_loans' => Loan::where('branch_id', $userBranch)
                                     ->where('status', 'active')
                                     ->count(),
                'low_stock_items' => Inventory::where('branch_id', $userBranch)
                                             ->whereRaw('stock_quantity <= min_stock_level')
                                             ->count(),
                'overdue_loans' => Loan::where('branch_id', $userBranch)
                                      ->where('status', 'active')
                                      ->where('expected_return_date', '<', now())
                                      ->count(),
            ];

            $branchStats = null;
            $recentTransfers = Transfer::with(['fromBranch', 'toBranch'])
                                     ->where(function ($query) use ($userBranch) {
                                         $query->where('from_branch_id', $userBranch)
                                               ->orWhere('to_branch_id', $userBranch);
                                     })
                                     ->latest()
                                     ->take(5)
                                     ->get();
        }

        $lowStockItems = Inventory::with(['product', 'branch'])
                                 ->whereRaw('stock_quantity <= min_stock_level')
                                 ->when(!$user->isAdmin(), function ($query) use ($userBranch) {
                                     return $query->where('branch_id', $userBranch);
                                 })
                                 ->take(10)
                                 ->get();

        $recentSales = Sale::with(['user', 'branch', 'items.product'])
                          ->when(!$user->isAdmin(), function ($query) use ($userBranch) {
                              return $query->where('branch_id', $userBranch);
                          })
                          ->latest()
                          ->take(5)
                          ->get();

        $overdueLoans = Loan::with(['nwowUnit.product', 'branch'])
                           ->where('status', 'active')
                           ->where('expected_return_date', '<', now())
                           ->when(!$user->isAdmin(), function ($query) use ($userBranch) {
                               return $query->where('branch_id', $userBranch);
                           })
                           ->take(5)
                           ->get();

        return view('dashboard', compact(
            'stats',
            'branchStats',
            'lowStockItems',
            'recentSales',
            'recentTransfers',
            'overdueLoans'
        ));
    }
}
