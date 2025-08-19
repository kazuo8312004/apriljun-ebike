<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Unit Details: {{ $nwow->chassis_no }}
            </h2>
            <div class="flex space-x-2">
                @if($nwow->status === 'in_stock')
                    <a href="{{ route('nwow.edit', $nwow) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Edit Unit
                    </a>
                @endif
                <a href="{{ route('nwow.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Units
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto">
            <!-- Unit Details -->
            <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Unit Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Product</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $nwow->product->name }}</p>
                            <p class="text-xs text-gray-500">{{ $nwow->product->brand }} - {{ $nwow->product->model }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Branch</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $nwow->branch->name }}</p>
                            <p class="text-xs text-gray-500">{{ $nwow->branch->code }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Chassis Number</h4>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $nwow->chassis_no }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Color</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $nwow->color ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Motor Number</h4>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $nwow->motor_no ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Battery Number</h4>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $nwow->battery_no ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Controller Number</h4>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $nwow->controller_no ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Charger Number</h4>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $nwow->charger_no ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Remote Number</h4>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $nwow->remote_no ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Purchase Date</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $nwow->purchase_date->format('M d, Y') }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Purchase Price</h4>
                            <p class="mt-1 text-sm text-gray-900">₱{{ number_format($nwow->purchase_price, 2) }}</p>
                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Status</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($nwow->status === 'in_stock') bg-green-100 text-green-800
                                @elseif($nwow->status === 'sold') bg-blue-100 text-blue-800
                                @elseif($nwow->status === 'loaned') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $nwow->status)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- NWOW Export Format -->
            @if($nwow->status === 'sold' && $nwow->saleItems->count() > 0)
                @php
                    $saleItem = $nwow->saleItems->first();
                    $sale = $saleItem->sale;
                @endphp
                <div class="bg-blue-50 overflow-hidden shadow rounded-lg mb-6">
                    <div class="px-6 py-4 border-b border-blue-200">
                        <h3 class="text-lg leading-6 font-medium text-blue-900">NWOW Export Format</h3>
                        <p class="text-sm text-blue-600">Format ready for sending to NWOW company</p>
                    </div>
                    <div class="px-6 py-4">
                        <div class="bg-white border border-blue-200 rounded p-4">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div><strong>Date Sell:</strong> {{ $sale->sale_date->format('Y-m-d') }}</div>
                                <div><strong>Name Customer:</strong> {{ $sale->customer_name }}</div>
                                <div><strong>Number:</strong> {{ $sale->customer_phone }}</div>
                                <div><strong>Address:</strong> {{ $sale->customer_address }}</div>
                                <div><strong>Unit:</strong> {{ $nwow->product->name }}</div>
                                <div><strong>Color:</strong> {{ $nwow->color }}</div>
                                <div><strong>Package:</strong> {{ $sale->payment_method }}</div>
                                <div><strong>Chassis:</strong> {{ $nwow->chassis_no }}</div>
                                <div><strong>Motor:</strong> {{ $nwow->motor_no }}</div>
                                <div><strong>Battery:</strong> {{ $nwow->battery_no }}</div>
                                <div><strong>Controller:</strong> {{ $nwow->controller_no }}</div>
                                <div><strong>Charger:</strong> {{ $nwow->charger_no }}</div>
                                <div><strong>Remote:</strong> {{ $nwow->remote_no }}</div>
                            </div>
                            
                            <!-- Copy to Clipboard Button -->
                            <div class="mt-4">
                                <button onclick="copyNwowData()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Copy NWOW Format
                                </button>
                            </div>

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