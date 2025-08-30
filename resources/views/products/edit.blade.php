@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-900">Edit Product</h2>
        <p class="text-gray-600">Update product information</p>
    </div>

    <div class="card max-w-2xl">
        <form method="POST" action="{{ route('products.update', $product) }}">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">SKU *</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('sku') border-red-500 @enderror">
                    @error('sku')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Category *</label>
                    <select name="category_id" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('category_id') border-red-500 @enderror">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}"
                                    {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status *</label>
                    <select name="status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('status') border-red-500 @enderror">
                        <option value="active" {{ old('status', $product->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $product->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="discontinued" {{ old('status', $product->status) == 'discontinued' ? 'selected' : '' }}>Discontinued</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Selling Price *</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₱</span>
                        </div>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" required
                               class="pl-7 block w-full rounded-md border-gray-300 shadow-sm @error('price') border-red-500 @enderror">
                    </div>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Cost Price</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">₱</span>
                        </div>
                        <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" step="0.01" min="0"
                               class="pl-7 block w-full rounded-md border-gray-300 shadow-sm @error('cost_price') border-red-500 @enderror">
                    </div>
                    @error('cost_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Stock Quantity *</label>
                    <input type="number" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" min="0" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('stock_quantity') border-red-500 @enderror">
                    @error('stock_quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Minimum Stock Level *</label>
                    <input type="number" name="min_stock_level" value="{{ old('min_stock_level', $product->min_stock_level) }}" min="0" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('min_stock_level') border-red-500 @enderror">
                    @error('min_stock_level')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Brand</label>
                    <input type="text" name="brand" value="{{ old('brand', $product->brand) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('brand') border-red-500 @enderror">
                    @error('brand')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Model</label>
                    <input type="text" name="model" value="{{ old('model', $product->model) }}"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('model') border-red-500 @enderror">
                    @error('model')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 mt-6">
                <a href="{{ route('products.index') }}" class="btn" style="border: 1px solid #d1d5db;">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    Update Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
