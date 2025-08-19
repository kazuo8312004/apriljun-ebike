<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @if(auth()->user()->isAdmin())
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Branches</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_branches'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Units in Stock</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['total_units'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Units in Stock</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ $stats['units_in_stock'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Sales Today</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ auth()->user()->isAdmin() ? $stats['total_sales_today'] : $stats['sales_today'] }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Loans</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['active_loans'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Revenue Today</dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    ₱{{ number_format(auth()->user()->isAdmin() ? $stats['total_revenue_today'] : $stats['revenue_today'], 2) }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Sales -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Sales</h3>
                    <div class="space-y-3">
                        @forelse($recentSales as $sale)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $sale->customer_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $sale->sale_number }} • {{ $sale->sale_date->format('M d, Y') }}</p>
                                    <p class="text-xs text-blue-600">{{ $sale->branch->name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-green-600">₱{{ number_format($sale->final_amount, 2) }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ $sale->payment_method }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No recent sales</p>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('sales.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            View all sales →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Low Stock Items</h3>
                    <div class="space-y-3">
                        @forelse($lowStockItems as $item)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $item->branch->name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-red-600">{{ $item->stock_quantity }}</p>
                                    <p class="text-xs text-gray-500">Min: {{ $item->min_stock_level }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">All items are well stocked</p>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('inventory.index') }}?low_stock=1" class="text-red-600 hover:text-red-500 text-sm font-medium">
                            View all low stock →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if(auth()->user()->isAdmin() && isset($branchStats))
        <!-- Branch Statistics (Admin Only) -->
        <div class="mt-8 bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Branch Overview</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Units in Stock</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sales Today</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Active Loans</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($branchStats as $branch)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $branch->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $branch->code }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $branch->units_in_stock }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $branch->sales_today }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $branch->active_loans }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Transfers & Overdue Loans -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
            <!-- Recent Transfers -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recent Transfers</h3>
                    <div class="space-y-3">
                        @forelse($recentTransfers as $transfer)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $transfer->transfer_number }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $transfer->fromBranch->code }} → {{ $transfer->toBranch->code }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($transfer->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($transfer->status === 'in_transit') bg-blue-100 text-blue-800
                                        @elseif($transfer->status === 'completed') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($transfer->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No recent transfers</p>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('transfers.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            View all transfers →
                        </a>
                    </div>
                </div>
            </div>

            <!-- Overdue Loans -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Overdue Loans</h3>
                    <div class="space-y-3">
                        @forelse($overdueLoans as $loan)
                            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $loan->borrower_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $loan->loan_number }}</p>
                                    <p class="text-xs text-blue-600">{{ $loan->branch->name }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-red-600">{{ $loan->getDaysOverdue() }} days</p>
                                    <p class="text-xs text-gray-500">Due: {{ $loan->expected_return_date->format('M d') }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No overdue loans</p>
                        @endforelse
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('loans.index') }}?overdue=1" class="text-red-600 hover:text-red-500 text-sm font-medium">
                            View all overdue →
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>