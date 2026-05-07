<!DOCTYPE html>
<html lang="en" class="h-full" id="htmlRoot">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Dashboard') — WMS Pro</title>
  <link rel="manifest" href="/manifest.json">
  <meta name="theme-color" content="#2563eb">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="WMS Pro">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  @vite(['resources/css/app.css','resources/js/app.js'])
  <style>
    /* Dark mode overrides */
    .dark body { background-color: #111827; color: #f3f4f6; }
    .dark .card { background: #1f2937; border-color: #374151; }
    .dark .card-header { border-color: #374151; }
    .dark .form-input, .dark .form-select { background: #374151; border-color: #4b5563; color: #f3f4f6; }
    .dark header { background: #1f2937; border-color: #374151; }
    .dark .table thead th { background: #374151; color: #d1d5db; border-color: #4b5563; }
    .dark .table tbody td { border-color: #374151; color: #e5e7eb; }
    .dark .table tbody tr:hover td { background: #374151; }
    .dark .bg-gray-50 { background-color: #111827; }
    .dark .bg-white { background-color: #1f2937; }
    .dark .text-gray-800, .dark .text-gray-700, .dark .text-gray-900 { color: #f3f4f6; }
    .dark .text-gray-500, .dark .text-gray-400 { color: #9ca3af; }
    .dark .border-gray-100, .dark .border-gray-200 { border-color: #374151; }
    .dark .btn-outline { background: #374151; border-color: #4b5563; color: #d1d5db; }
    .dark .btn-outline:hover { background: #4b5563; }
  </style>
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

  <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5 text-sm">
    <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <i class="fas fa-chart-pie w-5 text-center flex-shrink-0"></i><span>Dashboard</span>
    </a>

    <p class="sidebar-group-title">Inventory Management</p>
    <a href="{{ route('warehouses.index') }}" class="sidebar-item {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
      <i class="fas fa-warehouse w-5 text-center flex-shrink-0"></i><span>Warehouses</span>
    </a>
    <a href="{{ route('categories.index') }}" class="sidebar-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
      <i class="fas fa-boxes-stacked w-5 text-center flex-shrink-0"></i><span>Categories</span>
    </a>
    <a href="{{ route('units.index') }}" class="sidebar-item {{ request()->routeIs('units.*') ? 'active' : '' }}">
      <i class="fas fa-ruler w-5 text-center flex-shrink-0"></i><span>Units</span>
    </a>
    <a href="{{ route('items.index') }}" class="sidebar-item {{ request()->routeIs('items.*') ? 'active' : '' }}">
      <i class="fas fa-boxes-stacked w-5 text-center flex-shrink-0"></i><span>Products</span>
    </a>
    <a href="{{ route('stock-transfers.index') }}" class="sidebar-item {{ request()->routeIs('stock-transfers.*') ? 'active' : '' }}">
      <i class="fas fa-right-left w-5 text-center flex-shrink-0"></i><span>Stock Transfers</span>
    </a>
    <a href="{{ route('inventory-adjustments.index') }}" class="sidebar-item {{ request()->routeIs('inventory-adjustments.*') ? 'active' : '' }}">
      <i class="fas fa-sliders w-5 text-center flex-shrink-0"></i><span>Adjustments</span>
    </a>
    <a href="{{ route('inventory-movements.index') }}" class="sidebar-item {{ request()->routeIs('inventory-movements.*') ? 'active' : '' }}">
      <i class="fas fa-chart-gantt w-5 text-center flex-shrink-0"></i><span>Movement Log</span>
    </a>

    <p class="sidebar-group-title">Purchases</p>
    <a href="{{ route('suppliers.index') }}" class="sidebar-item {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
      <i class="fas fa-truck w-5 text-center flex-shrink-0"></i><span>Suppliers</span>
    </a>
    <a href="{{ route('purchases.index') }}" class="sidebar-item {{ request()->routeIs('purchases.*') ? 'active' : '' }}">
      <i class="fas fa-cart-flatbed w-5 text-center flex-shrink-0"></i><span>Purchase Orders</span>
    </a>
    <a href="{{ route('purchase-returns.index') }}" class="sidebar-item {{ request()->routeIs('purchase-returns.*') ? 'active' : '' }}">
      <i class="fas fa-rotate-right w-5 text-center flex-shrink-0"></i><span>Purchase Returns</span>
    </a>

    <p class="sidebar-group-title">Sales</p>
    <a href="{{ route('customers.index') }}" class="sidebar-item {{ request()->routeIs('customers.*') ? 'active' : '' }}">
      <i class="fas fa-users w-5 text-center flex-shrink-0"></i><span>Customers</span>
    </a>
    <a href="{{ route('sales.index') }}" class="sidebar-item {{ request()->routeIs('sales.*') ? 'active' : '' }}">
      <i class="fas fa-receipt w-5 text-center flex-shrink-0"></i><span>Sales Invoices</span>
    </a>
    <a href="{{ route('sale-returns.index') }}" class="sidebar-item {{ request()->routeIs('sale-returns.*') ? 'active' : '' }}">
      <i class="fas fa-rotate-left w-5 text-center flex-shrink-0"></i><span>Sale Returns</span>
    </a>

    <p class="sidebar-group-title">Finance</p>
    <a href="{{ route('expenses.index') }}" class="sidebar-item {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
      <i class="fas fa-money-bill-wave w-5 text-center flex-shrink-0"></i><span>Expenses</span>
    </a>
    <a href="{{ route('reports.profit-loss') }}" class="sidebar-item {{ request()->routeIs('reports.profit-loss') ? 'active' : '' }}">
      <i class="fas fa-chart-line w-5 text-center flex-shrink-0"></i><span>Profit & Loss</span>
    </a>
    <a href="{{ route('reports.open-balance') }}" class="sidebar-item {{ request()->routeIs('reports.open-balance') ? 'active' : '' }}">
      <i class="fas fa-scale-balanced w-5 text-center flex-shrink-0"></i><span>Balance Sheet</span>
    </a>

    <p class="sidebar-group-title">Reports</p>
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
      <i class="fas fa-user-gear w-5 text-center flex-shrink-0"></i><span>Users & Roles</span>
    </a>
    <a href="{{ route('login-logs.index') }}" class="sidebar-item {{ request()->routeIs('login-logs.*') ? 'active' : '' }}">
      <i class="fas fa-clock-rotate-left w-5 text-center flex-shrink-0"></i><span>Login History</span>
    </a>
    <a href="{{ route('backup.index') }}" class="sidebar-item {{ request()->routeIs('backup.*') ? 'active' : '' }}">
      <i class="fas fa-database w-5 text-center flex-shrink-0"></i><span>Backup & Restore</span>
    </a>
    @endrole

    <p class="sidebar-group-title">System</p>
    <a href="{{ route('notifications.index') }}" class="sidebar-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
      <i class="fas fa-bell w-5 text-center flex-shrink-0"></i><span>Alerts</span>
    </a>
    <a href="{{ route('settings.index') }}" class="sidebar-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
      <i class="fas fa-gear w-5 text-center flex-shrink-0"></i><span>Settings</span>
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
      <a href="{{ route('profile.show') }}" class="flex-shrink-0" title="Profile">
        <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
      </a>
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
      <h1 class="text-base font-semibold text-gray-800 hidden md:block">@yield('page-title','Dashboard')</h1>
    </div>
    <div class="flex items-center gap-3">
      <span class="text-xs text-gray-400 hidden sm:block">{{ now()->format('d M Y') }}</span>

      {{-- Dark Mode Toggle --}}
      <button id="darkToggle" title="Toggle dark mode" class="w-8 h-8 rounded-lg border border-gray-200 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors">
        <i class="fas fa-moon text-sm dark-icon-moon"></i>
        <i class="fas fa-sun text-sm dark-icon-sun hidden"></i>
      </button>

      {{-- PWA Install --}}
      <button id="installBtn" title="Install App" class="hidden w-8 h-8 rounded-lg border border-blue-200 bg-blue-50 flex items-center justify-center text-blue-600 hover:bg-blue-100 transition-colors">
        <i class="fas fa-mobile-screen-button text-sm"></i>
      </button>

      @php $lowStock = \App\Models\Inventory::whereRaw('quantity <= (SELECT alert_quantity FROM items WHERE items.id = inventory.item_id)')->count(); @endphp
      @if($lowStock > 0)
      <a href="{{ route('reports.stock') }}" class="relative text-amber-500">
        <i class="fas fa-bell text-lg"></i>
        <span class="absolute -top-1.5 -right-1.5 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center leading-none font-bold">{{ $lowStock }}</span>
      </a>
      @endif
      <a href="{{ route('profile.show') }}" title="Profile">
        <img src="{{ auth()->user()->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
      </a>
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

<script>
// Dark mode
(function(){
  const html = document.getElementById('htmlRoot');
  const btn  = document.getElementById('darkToggle');
  const moonI = document.querySelector('.dark-icon-moon');
  const sunI  = document.querySelector('.dark-icon-sun');
  const isDark = localStorage.getItem('wms-dark') === '1';
  if(isDark){ html.classList.add('dark'); moonI.classList.add('hidden'); sunI.classList.remove('hidden'); }
  btn.addEventListener('click', function(){
    const nowDark = html.classList.toggle('dark');
    localStorage.setItem('wms-dark', nowDark ? '1' : '0');
    moonI.classList.toggle('hidden', nowDark);
    sunI.classList.toggle('hidden', !nowDark);
  });
})();

// PWA Install prompt
let deferredPrompt;
window.addEventListener('beforeinstallprompt', e => {
  e.preventDefault();
  deferredPrompt = e;
  const btn = document.getElementById('installBtn');
  if(btn){ btn.classList.remove('hidden'); btn.addEventListener('click', () => { deferredPrompt.prompt(); deferredPrompt.userChoice.then(()=>{ deferredPrompt=null; btn.classList.add('hidden'); }); }); }
});

// Register service worker
if('serviceWorker' in navigator){
  navigator.serviceWorker.register('/sw.js').catch(()=>{});
}
</script>

<script>
// Runtime config for JS (base-path safe URLs)
window.WMS_CONFIG = {
  itemSearchUrl: @json(route('ajax.items.search')),
  itemStockUrl: @json(route('ajax.items.stock-warehouse')),
};
</script>

@stack('scripts')
</body>
</html>
