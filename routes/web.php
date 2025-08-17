<?php
// routes/web.php

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\NwowController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;
// Redirect root to dashboard if authenticated, to login if not
Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Branches (Admin only)
    Route::middleware(['admin'])->group(function () {
        Route::resource('branches', BranchController::class);
    });

    // Products
    Route::resource('products', ProductController::class);

    // Categories
    Route::resource('categories', CategoryController::class);

    // Inventory Management
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::post('/inventory/adjust', [InventoryController::class, 'adjust'])->name('inventory.adjust');
    Route::get('/inventory/branch/{branch}', [InventoryController::class, 'byBranch'])->name('inventory.branch');

    // NWOW (Unit Tracking)
    Route::resource('nwow', NwowController::class);

    // Transfers
    Route::resource('transfers', TransferController::class)->except(['edit', 'update']);
    Route::patch('/transfers/{transfer}/approve', [TransferController::class, 'approve'])->name('transfers.approve');
    Route::patch('/transfers/{transfer}/receive', [TransferController::class, 'receive'])->name('transfers.receive');
    Route::patch('/transfers/{transfer}/cancel', [TransferController::class, 'cancel'])->name('transfers.cancel');
    Route::get('/api/branch/{branch}/items', [TransferController::class, 'getBranchItems'])->name('api.branch.items');

    // Loans
    Route::resource('loans', LoanController::class)->except(['destroy']);
    Route::patch('/loans/{loan}/return', [LoanController::class, 'return'])->name('loans.return');
    Route::patch('/loans/{loan}/mark-lost', [LoanController::class, 'markLost'])->name('loans.mark-lost');

    // Sales
    Route::resource('sales', SaleController::class);
    Route::patch('/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
    Route::get('/api/products/{product}/price', [SaleController::class, 'getProductPrice'])->name('api.products.price');
    Route::get('/api/branch/{branch}/units', [SaleController::class, 'getBranchUnits'])->name('api.branch.units');

    // Profile routes (from Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';