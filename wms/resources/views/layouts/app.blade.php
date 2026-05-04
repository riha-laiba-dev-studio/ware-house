<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Dashboard') — WMS Pro</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="h-full flex bg-gray-50">

<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-20 hidden lg:hidden"></div>

<aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-slate-900 z-30 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 flex flex-col">
  <div class="flex items-center gap-3 px-5 py-5 border-b border-slate-700">
    <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
      <i class="fas fa-warehouse text-white text-sm"></i>
    </div>
    <div>
      <p class="text-white font-bold text-sm leading-none">WMS Pro</p>
      <p class="text-slate-400 text-xs mt-0.5">Warehouse Management</p>
    </div>
  </div>

  <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
    <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <i class="fas fa-chart-pie w-5 text-center flex-shrink-0"></i><span>Dashboard</span>
    </a>

    <p class="sidebar-group-title">Inventory Management</p>
    <a href="{{ route('warehouses.index') }}" class="sidebar-item {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
      <i class="fas fa-warehouse w-5 text-center flex-shrink-0"></i><span>Manage Warehouses</span>
    </a>
    <a href="{{ route('items.index') }}" class="sidebar-item {{ request()->routeIs('items.*') ? 'active' : '' }}">
      <i class="fas fa-boxes-stacked w-5 text-center flex-shrink-0"></i><span>Listing Product</span>
    </a>
    <a href="{{ route('stock-transfers.index') }}" class="sidebar-item {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}">
      <i class="fas fa-right-left w-5 text-center flex-shrink-0"></i><span>Stock Transfer</span>
    </a>
    <a href="{{ route('inventory-adjustments.index') }}" class="sidebar-item {{ request()->routeIs('inventory-adjustments.*') ? 'active' : '' }}">
      <i class="fas fa-sliders w-5 text-center flex-shrink-0"></i><span>Stock Listing</span>
    </a>

    <p class="sidebar-group-title">Sales & Purchase</p>
    <a href="{{ route('suppliers.index') }}" class="sidebar-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
      <i class="fas fa-truck w-5 text-center flex-shrink-0"></i><span>Manage Vendor</span>
    </a>
    <a href="{{ route('purchases.index') }}" class="sidebar-item {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
      <i class="fas fa-cart-flatbed w-5 text-center flex-shrink-0"></i><span>Purchase</span>
    </a>
    <a href="{{ route('customers.index') }}" class="sidebar-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
      <i class="fas fa-users w-5 text-center flex-shrink-0"></i><span>Manage Customer</span>
    </a>
    <a href="{{ route('sales.index') }}" class="sidebar-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
      <i class="fas fa-receipt w-5 text-center flex-shrink-0"></i><span>Sales</span>
    </a>

    <p class="sidebar-group-title">Expenses Management</p>
    <a href="{{ route('expenses.index') }}" class="sidebar-item {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
      <i class="fas fa-money-bill-wave w-5 text-center flex-shrink-0"></i><span>Manage Expense</span>
    </a>

    <p class="sidebar-group-title">Profit & Loss Management</p>
    <a href="{{ route('reports.open-balance') }}" class="sidebar-item {{ request()->routeIs('reports.open-balance') ? 'active' : '' }}">
      <i class="fas fa-scale-balanced w-5 text-center flex-shrink-0"></i><span>Open Balance Sheet</span>
    </a>
    <a href="{{ route('reports.profit-loss') }}" class="sidebar-item {{ request()->routeIs('reports.profit-loss') ? 'active' : '' }}">
      <i class="fas fa-chart-line w-5 text-center flex-shrink-0"></i><span>P&L Report</span>
    </a>
    <a href="{{ route('reports.sales') }}" class="sidebar-item {{ request()->routeIs('reports.sales') ? 'active' : '' }}">
      <i class="fas fa-file-invoice w-5 text-center flex-shrink-0"></i><span>Sales Report</span>
    </a>
    <a href="{{ route('reports.purchases') }}" class="sidebar-item {{ request()->routeIs('reports.purchases') ? 'active' : '' }}">
      <i class="fas fa-file-invoice-dollar w-5 text-center flex-shrink-0"></i><span>Purchase Report</span>
    </a>
    <a href="{{ route('reports.stock') }}" class="sidebar-item {{ request()->routeIs('reports.stock') ? 'active' : '' }}">
      <i class="fas fa-boxes w-5 text-center flex-shrink-0"></i><span>Stock Report</span>
    </a>

    @role('Admin')
    <p class="sidebar-group-title">Administration</p>
    <a href="{{ route('users.index') }}" class="sidebar-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
      <i class="fas fa-user-gear w-5 text-center flex-shrink-0"></i><span>Manage Roles</span>
    </a>
    @endrole

    <p class="sidebar-group-title">Account</p>
    <a href="#" class="sidebar-item active">
      <i class="fas fa-gear w-5 text-center flex-shrink-0"></i><span>Account Setting</span>
    </a>
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="sidebar-item w-full text-left text-red-400 hover:text-red-300 hover:bg-red-900/30">
        <i class="fas fa-right-from-bracket w-5 text-center flex-shrink-0"></i><span>Logout</span>
      </button>
    </form>
  </nav>

  <div class="px-4 py-3 border-t border-slate-700">
    <div class="flex items-center gap-3">
      <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
      <div class="flex-1 min-w-0">
        <p class="text-white text-xs font-semibold truncate">{{ auth()->user()->name }}</p>
        <p class="text-slate-400 text-xs truncate">{{ auth()->user()->getRoleNames()->first() ?? 'User' }}</p>
      </div>
    </div>
  </div>
</aside>

<div class="flex-1 flex flex-col min-w-0 lg:pl-64">
  <header class="bg-white border-b border-gray-200 px-4 lg:px-6 py-3 flex items-center gap-4 sticky top-0 z-10">
    <button id="sidebarToggle" class="lg:hidden text-gray-500">
      <i class="fas fa-bars text-xl"></i>
    </button>
    <div class="flex-1 flex items-center gap-3">
      <div class="relative hidden md:block">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
        <input type="text" placeholder="Search..." class="pl-8 pr-4 py-1.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 w-56 bg-gray-50">
      </div>
    </div>
    <div class="flex items-center gap-3">
      <span class="text-xs text-gray-400 hidden sm:block">{{ now()->format('d M Y') }}</span>
      @php $lowStock = \App\Models\Inventory::whereRaw('quantity <= (SELECT alert_quantity FROM items WHERE items.id = inventory.item_id)')->count(); @endphp
      @if($lowStock > 0)
      <a href="{{ route('reports.stock') }}" class="relative text-amber-500">
        <i class="fas fa-bell text-lg"></i>
        <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center leading-none font-bold">{{ $lowStock }}</span>
      </a>
      @endif
      <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
    </div>
  </header>

  <main class="flex-1 p-4 lg:p-6">
    @if(session('success'))
    <div class="auto-dismiss mb-4 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg text-sm">
      <i class="fas fa-check-circle text-emerald-500"></i> {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="auto-dismiss mb-4 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
      <i class="fas fa-exclamation-circle text-red-500"></i> {{ session('error') }}
    </div>
    @endif
    @if($errors->any())
    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg text-sm">
      <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif
    @yield('content')
  </main>
</div>

@stack('scripts')
</body>
</html>
