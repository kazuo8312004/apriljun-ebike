@extends('layouts.app')

@section('title', 'Sale Details')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Sale Details</h2>
            <p class="text-gray-600">{{ $sale->sale_number }}</p>
        </div>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="btn btn-primary text-sm">
                Print Receipt
            </button>
            @if($sale->status === 'pending')
                <a href="{{ route('sales.edit', $sale) }}" class="btn btn-success text-sm">
                    Edit Sale
                </a>
            @endif
            <a href="{{ route('sales.index') }}" class="btn" style="border: 1px solid #d1d5db;">
                Back to Sales
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sale Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Info -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $sale->customer_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $sale->customer_email ?: 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Phone</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $sale->customer_phone ?: 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Payment Method</label>
                        <p class="mt-1 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $sale->payment_method) }}</p>
                    </div>
                </div>
            </div>

            <!-- Sale Items -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Items Purchased</h3>
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Unit Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                                <tr>
                                    <td>
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $item->product->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $item->product->sku }}</p>
                                        </div>
                                    </td>
                                    <td>{{ $item->product->category->name }}</td>
                                    <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>₱{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($sale->notes)
                <!-- Notes -->
                <div class="card">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Notes</h3>
                    <p class="text-gray-700">{{ $sale->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Sale Summary -->
        <div class="space-y-6">
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sale Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="text-gray-900">₱{{ number_format($sale->total_amount, 2) }}</span>
                    </div>
                    @if($sale->discount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>Discount</span>
                            <span>-₱{{ number_format($sale->discount, 2) }}</span>
                        </div>
                    @endif
                    <div class="border-t pt-3">
                        <div class="flex justify-between text-lg font-medium">
                            <span class="text-gray-900">Total</span>
                            <span class="text-gray-900">₱{{ number_format($sale->final_amount, 2) }}</span>
                        </div>
                    </div>

                    <div class="mt-4 pt-4 border-t">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Status</span>
                            <span class="badge
                                {{ $sale->status === 'completed' ? 'badge-success' : ($sale->status === 'pending' ? 'badge-warning' : 'badge-danger') }}">
                                {{ ucfirst($sale->status) }}
                            </span>
                        </div>
                        <div class="flex justify-between text-sm mt-2">
                            <span class="text-gray-500">Sale Date</span>
                            <span class="text-gray-900">{{ $sale->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-2">
                            <span class="text-gray-500">Processed by</span>
                            <span class="text-gray-900">{{ $sale->user->name }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($sale->status !== 'cancelled')
                <div class="card">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                    <div class="space-y-2">
                        @if($sale->status === 'pending')
                            <a href="{{ route('sales.edit', $sale) }}"
                               class="btn btn-success w-full text-center">
                                Edit Sale
                            </a>
                        @endif

                        <form method="POST" action="{{ route('sales.cancel', $sale) }}"
                              onsubmit="return confirm('Are you sure you want to cancel this sale? Stock will be restored.')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-danger w-full">
                                Cancel Sale
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    body { background: white !important; }
    .card { box-shadow: none !important; border: 1px solid #ddd !important; }
}
</style>
@endsection
