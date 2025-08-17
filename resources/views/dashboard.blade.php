// resources/views/dashboard.blade.php

@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    @if(Auth::user()->isAdmin())
        <!-- Admin Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">🏢</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Active Branches</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_branches'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">🏷️</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Units in Stock</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_units'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">🔄</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Pending Transfers</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_transfers'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">⚠️</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Overdue Loans</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['overdue_loans'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Branch Overview -->
        <div class="card">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Branch Overview</h3>
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>Units in Stock</th>
                            <th>Sales Today</th>
                            <th>Active Loans</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branchStats as $branch)
                            <tr>
                                <td>
                                    <div class="flex items-center">
                                        <span class="branch-indicator branch-{{ strtolower($branch->code) }}">
                                            {{ $branch->code }}
                                        </span>
                                        <span class="ml-2">{{ $branch->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $branch->units_in_stock }}</td>
                                <td>{{ $branch->sales_today }}</td>
                                <td>{{ $branch->active_loans }}</td>
                                <td>
                                    <span class="badge badge-success">Active</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Branch Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">🏷️</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Units in Stock</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['units_in_stock'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">💰</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Today's Revenue</p>
                        <p class="text-2xl font-semibold text-gray-900">₱{{ number_format($stats['revenue_today'], 2) }}</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold text-xl">🤝</span>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Active Loans</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['active_loans'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Items -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Low Stock Items</h3>
                <a href="{{ route('inventory.index', ['low_stock' => 1]) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All
                </a>
            </div>

            @if($lowStockItems->count() > 0)
                <div class="space-y-3">
                    @foreach($lowStockItems as $inventory)
                        <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $inventory->product->name }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $inventory->branch->name }} ({{ $inventory->branch->code }})
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-red-600">{{ $inventory->stock_quantity }} left</p>
                                <p class="text-xs text-gray-500">Min: {{ $inventory->min_stock_level }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No low stock items!</p>
            @endif
        </div>

        <!-- Recent Sales -->
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Recent Sales</h3>
                <a href="{{ route('sales.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All
                </a>
            </div>

            @if($recentSales->count() > 0)
                <div class="space-y-3">
                    @foreach($recentSales as $sale)
                        <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $sale->sale_number }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $sale->customer_name }}
                                    @if(Auth::user()->isAdmin())
                                        - {{ $sale->branch->code }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-green-600">₱{{ number_format($sale->final_amount, 2) }}</p>
                                <p class="text-xs text-gray-500">{{ $sale->sale_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-4">No recent sales!</p>
            @endif
        </div>
    </div>

    @if($recentTransfers->count() > 0 || $overdueLoans->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Transfers -->
        @if($recentTransfers->count() > 0)
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Recent Transfers</h3>
                <a href="{{ route('transfers.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All
                </a>
            </div>

            <div class="space-y-3">
                @foreach($recentTransfers as $transfer)
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $transfer->transfer_number }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $transfer->fromBranch->code }} → {{ $transfer->toBranch->code }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="badge
                                {{ $transfer->status === 'completed' ? 'badge-success' :
                                   ($transfer->status === 'pending' ? 'badge-warning' : 'badge-info') }}">
                                {{ ucfirst($transfer->status) }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $transfer->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Overdue Loans -->
        @if($overdueLoans->count() > 0)
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Overdue Loans</h3>
                <a href="{{ route('loans.index', ['overdue' => 1]) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All
                </a>
            </div>

            <div class="space-y-3">
                @foreach($overdueLoans as $loan)
                    <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                        <div>
                            <p class="font-medium text-gray-900">{{ $loan->loan_number }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $loan->borrower_name }}
                                @if(Auth::user()->isAdmin())
                                    - {{ $loan->branch->code }}
                                @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-red-600">
                                {{ $loan->getDaysOverdue() }} days overdue
                            </p>
                            <p class="text-xs text-gray-500">Due: {{ $loan->expected_return_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
