@extends('layouts.app')

@section('title', 'Product Details')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">{{ $product->name }}</h2>
            <p class="text-gray-600">{{ $product->sku }}</p>
        </div>
        <div class="flex space-x-2">
            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                Edit Product
            </a>
            <a href="{{ route('products.index') }}" class="btn" style="border: 1px solid #d1d5db;">
                Back to Products
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Information -->
        <div class="lg:col-span-2 space-y-6">
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Product Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Product Name</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">SKU</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->sku }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Category</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->category->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <span
                            class="mt-1 badge {{ $product->status === 'active' ? 'badge-success' : ($product->status === 'inactive' ? 'badge-warning' : 'badge-danger') }}">
                            {{ ucfirst($product->status) }}
                        </span>
                    </div>
                    @if($product->brand)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Brand</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->brand }}</p>
                    </div>
                    @endif
                    @if($product->model)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Model</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->model }}</p>
                    </div>
                    @endif
                </div>

                @if($product->description)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-500">Description</label>
                    <p class="mt-1 text-sm text-gray-900">{{ $product->description }}</p>
                </div>
                @endif
            </div>

            <!-- Stock History (placeholder for future feature) -->
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                <p class="text-gray-500">Stock movement history will be displayed here in future updates.</p>
            </div>
        </div>

        <!-- Pricing & Stock -->
        <div class="space-y-6">
            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Selling Price</label>
                        <p class="text-2xl font-bold text-gray-900">₱{{ number_format($product->price, 2) }}</p>
                    </div>
                    @if($product->cost_price)
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Cost Price</label>
                        <p class="text-lg text-gray-900">₱{{ number_format($product->cost_price, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Profit Margin</label>
                        <p class="text-lg text-green-600">
                            ₱{{ number_format($product->price - $product->cost_price, 2) }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Stock Information</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Current Stock</label>
                        <p class="text-2xl font-bold {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $product->stock_quantity }}
                        </p>
                        @if($product->isLowStock())
                        <p class="text-sm text-red-600 mt-1">⚠️ Low Stock Alert</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Minimum Stock Level</label>
                        <p class="text-lg text-gray-900">{{ $product->min_stock_level }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Stock Status</label>
                        @if($product->stock_quantity == 0)
                        <span class="badge badge-danger">Out of Stock</span>
                        @elseif($product->isLowStock())