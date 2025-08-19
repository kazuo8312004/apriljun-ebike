<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sale Details: {{ $sale->sale_number }}
            </h2>
            <div class="flex space-x-2">
                @if($sale->status === 'completed')
                    <a href="{{ route('sales.edit', $sale) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Edit Sale
                    </a>
                @endif
                <a href="{{ route('sales.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Sales
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto">
            <!-- Sale Information -->
            <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Sale Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Sale Number</h4>
                            <p class="mt-1 text-sm text-gray-900 font-mono">{{ $sale->sale_number }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Sale Date</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->sale_date->format('M d, Y') }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Status</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($sale->status === 'completed') bg-green-100 text-green-800
                                @elseif($sale->status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($sale->status) }}
                            </span>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Branch</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->branch->name }}</p>
                            <p class="text-xs text-gray-500">{{ $sale->branch->code }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Staff</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->user->name }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Payment Method</h4>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($sale->payment_method === 'cash') bg-green-100 text-green-800
                                @elseif($sale->payment_method === 'installment') bg-yellow-100 text-yellow-800
                                @elseif($sale->payment_method === 'home_credit') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Customer Information</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Name</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->customer_name }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Phone</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->customer_phone ?: 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Email</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->customer_email ?: 'N/A' }}</p>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500">Address</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->customer_address ?: 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sale Items -->
            <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Sale Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($sale->items as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $item->product->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $item->product->brand }} - {{ $item->product->model }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($item->nwowUnit)
                                            <div class="text-sm text-gray-900">
                                                <div><strong>Chassis:</strong> {{ $item->nwowUnit->chassis_no }}</div>
                                                <div><strong>Color:</strong> {{ $item->nwowUnit->color ?: 'N/A' }}</div>
                                                @if($item->nwowUnit->motor_no)
                                                    <div><strong>Motor:</strong> {{ $item->nwowUnit->motor_no }}</div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-500">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₱{{ number_format($item->unit_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                        ₱{{ number_format($item->total_price, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Sale Summary -->
            <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Sale Summary</h3>
                </div>
                <div class="px-6 py-4">
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Subtotal:</span>
                            <span class="text-sm font-medium">₱{{ number_format($sale->total_amount, 2) }}</span>
                        </div>
                        
                        @if($sale->discount > 0)
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Discount:</span>
                                <span class="text-sm font-medium text-green-600">-₱{{ number_format($sale->discount, 2) }}</span>
                            </div>
                        @endif
                        
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between">
                                <span class="text-lg font-medium">Final Amount:</span>
                                <span class="text-lg font-bold text-green-600">₱{{ number_format($sale->final_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($sale->notes)
                        <div class="mt-6">
                            <h4 class="text-sm font-medium text-gray-500">Notes</h4>
                            <p class="mt-1 text-sm text-gray-900">{{ $sale->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- NWOW Export Data -->
            @if(!empty($nwowExportData))
                <div class="bg-blue-50 overflow-hidden shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-blue-200">
                        <h3 class="text-lg leading-6 font-medium text-blue-900">NWOW Export Data</h3>
                        <p class="text-sm text-blue-600">Data format for sending to NWOW company</p>
                    </div>
                    <div class="px-6 py-4">
                        @foreach($nwowExportData as $index => $data)
                            <div class="bg-white border border-blue-200 rounded p-4 mb-4">
                                <h4 class="font-medium text-blue-900 mb-2">Unit {{ $index + 1 }}</h4>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                                    <div><strong>Date Sell:</strong> {{ $data['date_sell'] }}</div>
                                    <div><strong>Customer:</strong> {{ $data['name_customer'] }}</div>
                                    <div><strong>Number:</strong> {{ $data['number'] }}</div>
                                    <div><strong>Address:</strong> {{ $data['address'] }}</div>
                                    <div><strong>Unit:</strong> {{ $data['unit'] }}</div>
                                    <div><strong>Color:</strong> {{ $data['color'] }}</div>
                                    <div><strong>Package:</strong> {{ $data['package'] }}</div>
                                    <div><strong>Chassis:</strong> {{ $data['chassis'] }}</div>
                                    <div><strong>Motor:</strong> {{ $data['motor'] }}</div>
                                    <div><strong>Battery:</strong> {{ $data['battery'] }}</div>
                                    <div><strong>Controller:</strong> {{ $data['controller'] }}</div>
                                    <div><strong>Charger:</strong> {{ $data['charger'] }}</div>
                                    <div><strong>Remote:</strong> {{ $data['remote'] }}</div>
                                </div>
                                
                                <!-- Copy button for each unit -->
                                <div class="mt-3">
                                    <button onclick="copyUnitData({{ $index }})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                        Copy Unit Data
                                    </button>
                                </div>
                                
                                <!-- Hidden textarea for copying -->
                                <textarea id="unit-data-{{ $index }}" class="hidden">{{ implode(', ', $data) }}</textarea>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function copyUnitData(index) {
            const textarea = document.getElementById(`unit-data-${index}`);
            textarea.style.display = 'block';
            textarea.select();
            textarea.setSelectionRange(0, 99999);
            document.execCommand('copy');
            textarea.style.display = 'none';
            
            alert('Unit data copied to clipboard!');
        }
    </script>
    @endpush
</x-app-layout>