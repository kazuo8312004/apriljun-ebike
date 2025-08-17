@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Categories</h2>
            <p class="text-gray-600">Manage product categories</p>
        </div>
        <a href="{{ route('categories.create') }}" class="btn btn-primary">
            Add New Category
        </a>
    </div>

    <div class="card">
        @if($categories->count() > 0)
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Products Count</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $category->name }}</p>
                                        @if($category->description)
                                            <p class="text-sm text-gray-500">{{ Str::limit($category->description, 50) }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $category->products_count }} products
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $category->is_active ? 'badge-success' : 'badge-danger' }}">
                                        {{ $category->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $category->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('categories.edit', $category) }}"
                                           class="text-green-600 hover:text-green-800 text-sm">Edit</a>
                                        @if($category->products_count == 0)
                                            <form method="POST" action="{{ route('categories.destroy', $category) }}"
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

            <div class="mt-4">
                {{ $categories->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">No categories found.</p>
                <a href="{{ route('categories.create') }}" class="btn btn-primary mt-4">
                    Create Your First Category
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
