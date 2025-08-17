{{-- resources/views/nwow/index.blade.php --}}

@extends('layouts.app')

@section('title', 'NWOW - Unit Tracking')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">NWOW - Unit Tracking</h2>
            <p class="text-gray-600">Track individual units with detailed information</p>
        </div>
        <a href="{{ route('nwow.create') }}" class="btn btn-primary">
            Add New Unit
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="card">
        <form onsubmit="return handleSearch(this)" action="{{ route('nwow.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Chassis, Motor, Battery..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                @if(Auth::user()->isAdmin())
                <div>
                    <label class="block text-sm font-medium text-gray-700">Branch</label>
                    <select name="branch_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}"
                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }} ({{ $branch->code }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700">Product</label>
                    <select name="product_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Products</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}"
                                    {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Status</option>
                        <option value="in_stock" {{ request('status') == 'in_stock' ? 'selected' : '' }}>In Stock</option>
                        <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="loaned" {{ request('status') == 'loaned' ? 'selected' : '' }}>Loaned</option>
                        <option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>Transferred</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <button type="submit" class="btn btn-primary mr-2">Search</button>
                    <a href="{{ route('nwow.index') }}" class="btn" style="border: 1px solid #d1d5db;">Clear</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Units Table -->
    <div class="card">
        @if($units->count() > 0)
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Unit Info</th>
                            <th>Product</th>
                            @if(Auth::user()->isAdmin())
                            <th>Branch</th>
                            @endif
                            <th>Chassis No.</th>
                            <th>Color</th>
                            <th>Purchase Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($units as $unit)
                            <tr>
                                <td>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $unit->product->name }}</p>
                                        <p class="text-sm text-gray-500">₱{{ number_format($unit->purchase_price, 2) }}</p>
                                    </div>
                                </td>
                                <td>{{ $unit->product->brand }} {{ $unit->product->model }}</td>
                                @if(Auth::user()->isAdmin())
                                <td>
                                    <span class="branch-indicator branch-{{ strtolower($unit->branch->code) }}">
                                        {{ $unit->branch->code }}
                                    </span>
                                </td>
                                @endif
                                <td>
                                    <span class="font-mono text-sm">{{ $unit->chassis_no }}</span>
                                </td>
                                <td>{{ $unit->color ?? 'N/A' }}</td>
                                <td>{{ $unit->purchase_date->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge
                                        {{ $unit->status === 'in_stock' ? 'badge-success' :
                                           ($unit->status === 'sold' ? 'badge-info' :
                                           ($unit->status === 'loaned' ? 'badge-warning' : 'badge-danger')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $unit->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('nwow.show', $unit) }}"
                                           class="text-blue-600 hover:text-blue-800 text-sm">View</a>

                                        @if($unit->status === 'in_stock')
                                            <a href="{{ route('nwow.edit', $unit) }}"
                                               class="text-green-600 hover:text-green-800 text-sm">Edit</a>

                                            <form method="POST" action="{{ route('nwow.destroy', $unit) }}"
                                                  style="display: inline;" onsubmit="return confirmDelete(event)">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 text-sm">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $units->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">No units found.</p>
                <a href="{{ route('nwow.create') }}" class="btn btn-primary mt-4">
                    Add Your First Unit
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
