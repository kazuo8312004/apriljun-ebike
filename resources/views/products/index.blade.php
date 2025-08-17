@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Products</h2>
            <p class="text-gray-600">Manage your e-bike inventory</p>
        </div>
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            Add New Product
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="card">
        <form onsubmit="return handleSearch(this)" action="{{ route('products.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search products..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Category</label>
                    <select name="category_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="discontinued" {{ request('status') == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                    </select>
                </div>

                <div class="flex items-end space-x-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                    <a href="{{ route('products.index') }}" class="btn" style="border: 1px solid #d1d5db;">Clear</a>
                </div>
            </div>

            <div class="mt-4">
                <label class="flex items-center">
                    <input type="checkbox" name="low_stock" value="1"
                           {{ request('low_stock') ? 'checked' : '' }}
                           class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-700">Show only low stock products</span>
                </label>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="card">
        @if($products->count() > 0)
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Product Info</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $product->name }}</p>
                                        <p class="text-sm text-gray-500">SKU: {{ $product->sku }}</p>
                                        @if($product->brand)
                                            <p class="text-xs text-gray-400">{{ $product->brand }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $product->category->name }}</td>
                                <td>₱{{ number_format($product->price, 2) }}</td>
                                <td>
                                    <div>
                                        <span class="font-medium {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                        @if($product->isLowStock())
                                            <span class="badge badge-warning ml-1">Low Stock</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge
                                        {{ $product->status === 'active' ? 'badge-success' : ($product->status === 'inactive' ? 'badge-warning' : 'badge-danger') }}">
                                        {{ ucfirst($product->status) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('products.show', $product) }}"
                                           class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                                        <a href="{{ route('products.edit', $product) }}"
                                           class="text-green-600 hover:text-green-800 text-sm">Edit</a>
                                        <form method="POST" action="{{ route('products.destroy', $product) }}"
                                              style="display: inline;" onsubmit="return confirmDelete(event)">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">No products found.</p>
                <a href="{{ route('products.create') }}" class="btn btn-primary mt-4">
                    Add Your First Product
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
