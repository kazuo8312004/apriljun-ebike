// resources/views/transfers/index.blade.php

@extends('layouts.app')

@section('title', 'Transfers')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Transfers</h2>
            <p class="text-gray-600">Manage inventory transfers between branches</p>
        </div>
        <a href="{{ route('transfers.create') }}" class="btn btn-primary">
            New Transfer
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="card">
        <form onsubmit="return handleSearch(this)" action="{{ route('transfers.index') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Transfer number..."
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
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

                <div class="flex items-end">
                    <button type="submit" class="btn btn-primary mr-2">Search</button>
                    <a href="{{ route('transfers.index') }}" class="btn" style="border: 1px solid #d1d5db;">Clear</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Transfers Table -->
    <div class="card">
        @if($transfers->count() > 0)
            <div class="overflow-x-auto">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Transfer Info</th>
                            <th>From → To</th>
                            <th>Items</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transfers as $transfer)
                            <tr>
                                <td>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $transfer->transfer_number }}</p>
                                        <p class="text-sm text-gray-500">By: {{ $transfer->user->name }}</p>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center">
                                        <span class="branch-indicator branch-{{ strtolower($transfer->fromBranch->code) }}">
                                            {{ $transfer->fromBranch->code }}
                                        </span>
                                        <span class="mx-2">→</span>
                                        <span class="branch-indicator branch-{{ strtolower($transfer->toBranch->code) }}">
                                            {{ $transfer->toBranch->code }}
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $transfer->items->count() }} item(s)
                                    </span>
                                </td>
                                <td>{{ $transfer->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <span class="badge
                                        {{ $transfer->status === 'completed' ? 'badge-success' :
                                           ($transfer->status === 'pending' ? 'badge-warning' :
                                           ($transfer->status === 'in_transit' ? 'badge-info' : 'badge-danger')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $transfer->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('transfers.show', $transfer) }}"
                                           class="text-blue-600 hover:text-blue-800 text-sm">View</a>

                                        @if($transfer->status === 'pending' && Auth::user()->canAccessBranch($transfer->from_branch_id))
                                            <form method="POST" action="{{ route('transfers.approve', $transfer) }}"
                                                  style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-green-600 hover:text-green-800 text-sm">
                                                    Approve
                                                </button>
                                            </form>
                                        @endif

                                        @if($transfer->status === 'in_transit' && Auth::user()->canAccessBranch($transfer->to_branch_id))
                                            <form method="POST" action="{{ route('transfers.receive', $transfer) }}"
                                                  style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">
                                                    Receive
                                                </button>
                                            </form>
                                        @endif

                                        @if(in_array($transfer->status, ['pending', 'in_transit']))
                                            <form method="POST" action="{{ route('transfers.cancel', $transfer) }}"
                                                  style="display: inline;" onsubmit="return confirmDelete(event)">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-800 text-sm">
                                                    Cancel
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
                {{ $transfers->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">No transfers found.</p>
                <a href="{{ route('transfers.create') }}" class="btn btn-primary mt-4">
                    Create Your First Transfer
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
