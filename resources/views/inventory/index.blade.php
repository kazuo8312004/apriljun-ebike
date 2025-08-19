<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inventory Management') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Items</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['total_items'] }}</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.502 0L4.312 14.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Low Stock Items</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['low_stock_items'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Out of Stock</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $stats['out_of_stock'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Product name, SKU..." 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                @if(auth()->user()->isAdmin())
                <div>
                    <label class="block text-sm font-medium text-gray-700">Branch</label>
                    <select name="branch_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700">Product Type</label>
                    <select name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Types</option>
                        <option value="unit" {{ request('type') == 'unit' ? 'selected' : '' }}>Units</option>
                        <option value="part" {{ request('type') == 'part' ? 'selected' : '' }}>Parts</option>
                        <option value="accessory" {{ request('type') == 'accessory' ? 'selected' : '' }}>Accessories</option>
                    </select>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label class="ml-2 block text-sm text-gray-900">Low Stock Only</label>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Inventory Table -->
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Level</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>                            <!-- Hidden text for copying -->
                            <textarea id="nwow-data" class="hidden">{{ $sale->sale_date->format('Y-m-d') }}, {{ $sale->customer_name }}, {{ $sale->customer_phone }}, {{ $sale->customer_address }}, {{ $nwow->product->name }}, {{ $nwow->color }}, {{ $sale->payment_method }}, {{ $nwow->chassis_no }}, {{ $nwow->motor_no }}, {{ $nwow->battery_no }}, {{ $nwow->controller_no }}, {{ $nwow->charger_no }}, {{ $nwow->remote_no }}</textarea>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Sales History -->
            @if($nwow->saleItems->count() > 0)
                <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Sales History</h3>
                    </div>
                    <div class="px-6 py-4">
                        @foreach($nwow->saleItems as $saleItem)
                            <div class="border border-gray-200 rounded p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Sale #{{ $saleItem->sale->sale_number }}</h4>
                                        <p class="text-sm text-gray-600">Date: {{ $saleItem->sale->sale_date->format('M d, Y') }}</p>
                                        <p class="text-sm text-gray-600">Customer: {{ $saleItem->sale->customer_name }}</p>
                                        <p class="text-sm text-gray-600">Phone: {{ $saleItem->sale->customer_phone }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Payment: {{ ucfirst($saleItem->sale->payment_method) }}</p>
                                        <p class="text-sm text-gray-600">Price: ₱{{ number_format($saleItem->unit_price, 2) }}</p>
                                        <p class="text-sm text-gray-600">Status: {{ ucfirst($saleItem->sale->status) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Loan History -->
            @if($nwow->loans->count() > 0)
                <div class="bg-white overflow-hidden shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">Loan History</h3>
                    </div>
                    <div class="px-6 py-4">
                        @foreach($nwow->loans as $loan)
                            <div class="border border-gray-200 rounded p-4 mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <h4 class="font-medium text-gray-900">Loan #{{ $loan->loan_number }}</h4>
                                        <p class="text-sm text-gray-600">Borrower: {{ $loan->borrower_name }}</p>
                                        <p class="text-sm text-gray-600">Phone: {{ $loan->borrower_phone }}</p>
                                        <p class="text-sm text-gray-600">Loan Date: {{ $loan->loan_date->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Expected Return: {{ $loan->expected_return_date->format('M d, Y') }}</p>
                                        @if($loan->actual_return_date)
                                            <p class="text-sm text-gray-600">Actual Return: {{ $loan->actual_return_date->format('M d, Y') }}</p>
                                        @endif
                                        <p class="text-sm text-gray-600">Collateral: ₱{{ number_format($loan->collateral_amount, 2) }}</p>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($loan->status === 'active') bg-yellow-100 text-yellow-800
                                            @elseif($loan->status === 'returned') bg-green-100 text-green-800
                                            @elseif($loan->status === 'overdue') bg-red-100 text-red-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function copyNwowData() {
            const textarea = document.getElementById('nwow-data');
            textarea.style.display = 'block';
            textarea.select();
            textarea.setSelectionRange(0, 99999);
            document.execCommand('copy');
            textarea.style.display = 'none';
            
            // Show success message
            alert('NWOW data copied to clipboard!');
        }
    </script>
    @endpush
</x-app-layout>