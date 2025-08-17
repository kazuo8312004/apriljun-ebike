@extends('layouts.app')

@section('title', 'New Sale')

@section('content')
<div class="space-y-6">
    <div>
        <h2 class="text-xl font-semibold text-gray-900">New Sale</h2>
        <p class="text-gray-600">Process a new e-bike sale</p>
    </div>

    <div class="card max-w-4xl">
        <form method="POST" action="{{ route('sales.store') }}" id="saleForm">
            @csrf

            <!-- Customer Information -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Customer Name *</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('customer_name') border-red-500 @enderror">
                        @error('customer_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('customer_email') border-red-500 @enderror">
                        @error('customer_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone') }}"
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
                    <!-- Sale items will be added here dynamically -->
                </div>

                @error('items')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sale Summary -->
            <div class="mb-8">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sale Summary</h3>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Payment Method *</label>
                            <select name="payment_method" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm @error('payment_method') border-red-500 @enderror">
                                <option value="">Select Payment Method</option# A.C.E E-bike Management System - Pure Laravel Development Guide

## Project Setup

### 1. Laravel Setup

```bash
# Create new Laravel project
composer create-project laravel/laravel ace-ebike-system
cd ace-ebike-system

# Install required packages
composer require spatie/laravel-permission
composer require maatwebsite/excel
composer require laravel/breeze --dev

# Install Breeze for basic auth scaffolding
php artisan breeze:install blade
npm install && npm run build

# Or install Laravel UI (alternative)
# composer require laravel/ui
# php artisan ui bootstrap --auth
