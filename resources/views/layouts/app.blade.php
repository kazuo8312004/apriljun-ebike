<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            box-shadow: 2px 0 4px rgba(0,0,0,0.1);
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #3b82f6;
            color: white;
            border: 1px solid #3b82f6;
        }

        .btn-primary:hover {
            background-color: #2563eb;
            border-color: #2563eb;
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
            border: 1px solid #ef4444;
        }

        .btn-success {
            background-color: #10b981;
            color: white;
            border: 1px solid #10b981;
        }

        .btn-warning {
            background-color: #f59e0b;
            color: white;
            border: 1px solid #f59e0b;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th, .table td {
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
        }

        .table th {
            background-color: #f9fafb;
            font-weight: 600;
        }

        .badge {
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }

        .badge-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .badge-info {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .branch-indicator {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .branch-sdo { background-color: #dbeafe; color: #1e40af; }
        .branch-bty { background-color: #dcfce7; color: #166534; }
        .branch-nrv { background-color: #fef3c7; color: #92400e; }
        .branch-snt { background-color: #fce7f3; color: #be185d; }
        .branch-sanj { background-color: #f3e8ff; color: #7c2d12; }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="sidebar w-64 bg-white">
            <div class="p-4">
                <h2 class="text-xl font-bold text-gray-800">A.C.E E-bike</h2>
                @if(Auth::user()->branch)
                    <span class="branch-indicator branch-{{ strtolower(Auth::user()->branch->code) }}">
                        {{ Auth::user()->branch->code }}
                    </span>
                @endif
            </div>

            <nav class="mt-4">
                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <span class="ml-2">📊 Dashboard</span>
                </a>

                @if(Auth::user()->isAdmin())
                <a href="{{ route('branches.index') }}"
                   class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('branches.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <span class="ml-2">🏢 Branches</span>
                </a>
                @endif

                <a href="{{ route('products.index') }}"
                   class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <span class="ml-2">📦 Products</span>
                </a>

                <a href="{{ route('categories.index') }}"
                   class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('categories.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <span class="ml-2">📂 Categories</span>
                </a>

                <a href="{{ route('inventory.index') }}"
                   class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('inventory.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <span class="ml-2">📋 Inventory</span>
                </a>

                <a href="{{ route('nwow.index') }}"
                   class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('nwow.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <span class="ml-2">🏷️ NWOW</span>
                </a>

                <a href="{{ route('transfers.index') }}"
                   class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('transfers.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <span class="ml-2">🔄 Transfers</span>
                </a>

                <a href="{{ route('loans.index') }}"
                   class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('loans.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <span class="ml-2">🤝 Loans</span>
                </a>

                <a href="{{ route('sales.index') }}"
                   class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('sales.*') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <span class="ml-2">💰 Sales</span>
                </a>
            </nav>

            <div class="absolute bottom-4 left-4 right-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-sm text-gray-600">{{ Auth::user()->name }}</span>
                        <span class="text-xs text-gray-400 block">{{ ucfirst(Auth::user()->role) }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Header -->
            <header class="bg-white shadow-sm border-b">
                <div class="px-6 py-4">
                    <h1 class="text-2xl font-semibold text-gray-900">@yield('title', 'Dashboard')</h1>
                </div>
            </header>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="m-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="m-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Page Content -->
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Custom JavaScript -->
    <script>
        // Confirm delete actions
        function confirmDelete(event) {
            if (!confirm('Are you sure you want to delete this item?')) {
                event.preventDefault();
            }
        }

        // Auto-hide flash messages
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
                alerts.forEach(alert => {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 5000);
        });

        // Search functionality
        function handleSearch(form) {
            const formData = new FormData(form);
            const params = new URLSearchParams();

            for (let [key, value] of formData.entries()) {
                if (value.trim() !== '') {
                    params.append(key, value);
                }
            }

            window.location.href = form.action + '?' + params.toString();
            return false;
        }

        // Dynamic form helpers
        function addSaleItem() {
            // Implementation for adding sale items dynamically
        }

        function addTransferItem() {
            // Implementation for adding transfer items dynamically
        }

        function updateProductInfo(selectElement) {
            const productId = selectElement.value;
            if (productId) {
                fetch(`/api/products/${productId}/price`)
                    .then(response => response.json())
                    .then(data => {
                        const container = selectElement.closest('.item-row');
                        const priceInput = container.querySelector('.unit-price');
                        const stockSpan = container.querySelector('.stock-info');

                        if (priceInput) priceInput.value = data.price;
                        if (stockSpan) stockSpan.textContent = `Stock: ${data.stock_quantity}`;
                    });
            }
        }
    </script>
</body>
</html>
