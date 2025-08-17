@extends('layouts.app')

@section('title', 'Edit Sale')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-900">Edit Sale</h2>
        <p class="text-gray-600">Update sale information - {{ $sale->sale_number }}</p>
    </div>

    <div class="card max-w-4xl">
        <form method="POST" action="{{ route('sales.update', $sale) }}" id="saleForm">
            @csrf
            @method('PATCH')

            <!-- Customer Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Customer Name *</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $sale->customer_name) }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('customer_name') border-red-500 @enderror">
                        @error('customer_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', $sale->customer_email) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('customer_email') border-red-500 @enderror">
                        @error('customer_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone', $sale->customer_phone) }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('customer_phone') border-red-500 @enderror">
                        @error('customer_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sale Items -->
            <div class="mb-8">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Sale Items</h3>
                    <button type="button" onclick="addSaleItem()" class="btn btn-primary text-sm">
                        Add Item
                    </button>
                </div>

                <div id="saleItems">
                    @foreach($sale->items as $index => $item)
                        <div class="sale-item mb-4 p-4 border border-gray-200 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Product</label>
                                    <select name="items[{{ $index }}][product_id]" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm product-select"
                                            onchange="updatePrice(this, {{ $index }})">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                    data-price="{{ $product->price }}"
                                                    data-stock="{{ $product->stock_quantity + ($product->id == $item->product_id ? $item->quantity : 0) }}"
                                                    {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                {{ $product->name }} (₱{{ number_format($product->price, 2) }}) - Stock: {{ $product->stock_quantity + ($product->id == $item->product_id ? $item->quantity : 0) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Quantity</label>
                                    <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}"
                                           min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm quantity-input"
                                           onchange="calculateItemTotal({{ $index }})">
                                </div>
                                <div class="flex items-end">
                                    <button type="button" onclick="removeSaleItem(this)"
                                            class="btn btn-danger text-sm w-full">
                                        Remove
                                    </button>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="text-sm text-gray-600">Item Total: ₱<span class="item-total">{{ number_format($item->total_price, 2) }}</span></span>
                                <input type="hidden" class="item-total-input" value="{{ $item->total_price }}">
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('items')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sale Summary -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sale Summary</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Payment Method *</label>
                            <select name="payment_method" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('payment_method') border-red-500 @enderror">
                                <option value="">Select Payment Method</option>
                                <option value="cash" {{ old('payment_method', $sale->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ old('payment_method', $sale->payment_method) == 'card' ? 'selected' : '' }}>Card</option>
                                <option value="bank_transfer" {{ old('payment_method', $sale->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="installment" {{ old('payment_method', $sale->payment_method) == 'installment' ? 'selected' : '' }}>Installment</option>
                            </select>
                            @error('payment_method')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Discount</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">₱</span>
                                </div>
                                <input type="number" name="discount" value="{{ old('discount', $sale->discount) }}"
                                       step="0.01" min="0" id="discountInput"
                                       class="pl-7 block w-full rounded-md border-gray-300 shadow-sm @error('discount') border-red-500 @enderror"
                                       onchange="calculateTotal()">
                            </div>
                            @error('discount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status *</label>
                            <select name="status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('status') border-red-500 @enderror">
                                <option value="pending" {{ old('status', $sale->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="completed" {{ old('status', $sale->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700">Notes</label>
                        <textarea name="notes" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('notes') border-red-500 @enderror">{{ old('notes', $sale->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Display -->
                    <div class="mt-6 pt-4 border-t">
                        <div class="text-right space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="text-gray-900">₱<span id="subtotal">{{ number_format($sale->total_amount, 2) }}</span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Discount:</span>
                                <span class="text-green-600">-₱<span id="discountDisplay">{{ number_format($sale->discount, 2) }}</span></span>
                            </div>
                            <div class="flex justify-between text-lg font-medium">
                                <span class="text-gray-900">Total:</span>
                                <span class="text-gray-900">₱<span id="total">{{ number_format($sale->final_amount, 2) }}</span></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4">
                <a href="{{ route('sales.show', $sale) }}" class="btn" style="border: 1px solid #d1d5db;">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    Update Sale
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let itemIndex = {{ $sale->items->count() }};

function addSaleItem() {
    const saleItems = document.getElementById('saleItems');
    const itemHtml = `
        <div class="sale-item mb-4 p-4 border border-gray-200 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Product</label>
                    <select name="items[${itemIndex}][product_id]" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm product-select"
                            onchange="updatePrice(this, ${itemIndex})">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                    data-price="{{ $product->price }}"
                                    data-stock="{{ $product->stock_quantity }}">
                                {{ $product->name }} (₱{{ number_format($product->price, 2) }}) - Stock: {{ $product->stock_quantity }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Quantity</label>
                    <input type="number" name="items[${itemIndex}][quantity]" value="1"
                           min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm quantity-input"
                           onchange="calculateItemTotal(${itemIndex})">
                </div>
                <div class="flex items-end">
                    <button type="button" onclick="removeSaleItem(this)"
                            class="btn btn-danger text-sm w-full">
                        Remove
                    </button>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-sm text-gray-600">Item Total: ₱<span class="item-total">0.00</span></span>
                <input type="hidden" class="item-total-input" value="0">
            </div>
        </div>
    `;

    saleItems.insertAdjacentHTML('beforeend', itemHtml);
    itemIndex++;
}

function removeSaleItem(button) {
    button.closest('.sale-item').remove();
    calculateTotal();
}

function updatePrice(select, index) {
    const option = select.options[select.selectedIndex];
    const price = option.dataset.price || 0;
    const stock = option.dataset.stock || 0;

    const quantityInput = select.closest('.sale-item').querySelector('.quantity-input');
    quantityInput.max = stock;

    calculateItemTotal(index);
}

function calculateItemTotal(index) {
    const saleItem = document.querySelectorAll('.sale-item')[index];
    if (!saleItem) return;

    const select = saleItem.querySelector('.product-select');
    const quantityInput = saleItem.querySelector('.quantity-input');
    const itemTotalSpan = saleItem.querySelector('.item-total');
    const itemTotalInput = saleItem.querySelector('.item-total-input');

    const option = select.options[select.selectedIndex];
    const price = parseFloat(option.dataset.price || 0);
    const quantity = parseInt(quantityInput.value || 0);
    const total = price * quantity;

    itemTotalSpan.textContent = total.toFixed(2);
    itemTotalInput.value = total;

    calculateTotal();
}

function calculateTotal() {
    let subtotal = 0;
    document.querySelectorAll('.item-total-input').forEach(input => {
        subtotal += parseFloat(input.value || 0);
    });

    const discount = parseFloat(document.getElementById('discountInput').value || 0);
    const total = subtotal - discount;

    document.getElementById('subtotal').textContent = subtotal.toFixed(2);
    document.getElementById('discountDisplay').textContent = discount.toFixed(2);
    document.getElementById('total').textContent = total.toFixed(2);
}

// Initialize calculations on page load
document.addEventListener('DOMContentLoaded', function() {
    calculateTotal();
});
</script>
@endsection
