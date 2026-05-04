@extends('layouts.app')
@section('title','Dashboard')
@section('page-title','Dashboard')

@section('content')
{{-- Summary Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="stat-card">
    <div class="stat-icon bg-blue-100 text-blue-600"><i class="fas fa-receipt"></i></div>
    <div>
      <p class="text-xs text-gray-500 font-medium">Net Sales (Month)</p>
      <p class="text-xl font-bold text-gray-800">PKR {{ number_format($stats['totalSales'],0) }}</p>
      <p class="text-xs text-gray-400">Today: PKR {{ number_format($stats['todaySales'],0) }}</p>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon bg-emerald-100 text-emerald-600"><i class="fas fa-cart-flatbed"></i></div>
    <div>
      <p class="text-xs text-gray-500 font-medium">Total Purchase</p>
      <p class="text-xl font-bold text-gray-800">PKR {{ number_format($stats['totalPurchases'],0) }}</p>
      <p class="text-xs text-gray-400">Today: PKR {{ number_format($stats['todayPurchases'],0) }}</p>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon bg-red-100 text-red-600"><i class="fas fa-money-bill-wave"></i></div>
    <div>
      <p class="text-xs text-gray-500 font-medium">Total Expense</p>
      <p class="text-xl font-bold text-gray-800">PKR {{ number_format($stats['totalExpenses'],0) }}</p>
      <p class="text-xs text-gray-400">This Month</p>
    </div>
  </div>
  <div class="stat-card">
    <div class="stat-icon {{ $stats['totalProfit'] >= 0 ? 'bg-violet-100 text-violet-600' : 'bg-red-100 text-red-600' }}">
      <i class="fas fa-chart-line"></i>
    </div>
    <div>
      <p class="text-xs text-gray-500 font-medium">Net Profit/Loss</p>
      <p class="text-xl font-bold {{ $stats['totalProfit'] >= 0 ? 'text-violet-700' : 'text-red-600' }}">
        PKR {{ number_format($stats['totalProfit'],0) }}
      </p>
      <p class="text-xs text-gray-400">This Month</p>
    </div>
  </div>
</div>

<!-- Chart + Low Stock Row -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
  {{-- Chart --}}
  <div class="card lg:col-span-2">
    <div class="card-header">
      <h2 class="text-sm font-semibold text-gray-700">Sales / Purchase / Expense / P&L (6 Months)</h2>
      <span class="badge badge-info">Monthly</span>
    </div>
    <div class="card-body">
      <canvas id="overviewChart" height="220"></canvas>
    </div>
  </div>

  {{-- Low Stock --}}
  <div class="card">
    <div class="card-header">
      <h2 class="text-sm font-semibold text-gray-700">Low Stock Alerts</h2>
      <a href="{{ route('reports.stock') }}" class="text-xs text-blue-600 hover:underline">View All</a>
    </div>
    <div class="card-body p-0">
      @forelse($lowStockItems->take(6) as $item)
      <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-50 last:border-0">
        <div>
          <p class="text-sm font-medium text-gray-800">{{ $item->name }}</p>
          <p class="text-xs text-gray-400">{{ $item->sku }}</p>
        </div>
        <div class="text-right">
          <span class="badge badge-danger">{{ number_format($item->total_stock,0) }} left</span>
          <p class="text-xs text-gray-400 mt-0.5">min: {{ $item->alert_quantity }}</p>
        </div>
      </div>
      @empty
      <div class="px-4 py-6 text-center text-gray-400 text-sm"><i class="fas fa-check-circle text-emerald-400 text-2xl mb-2 block"></i> All items are well-stocked</div>
      @endforelse
    </div>
  </div>
</div>

<!-- Top Vendors / Top Customers -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
  {{-- Top Vendors --}}
  <div class="card">
    <div class="card-header">
      <h2 class="text-sm font-semibold text-gray-700">Top Vendors</h2>
      <a href="{{ route('suppliers.index') }}" class="text-xs text-blue-600 hover:underline">View All</a>
    </div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>#</th><th>Vendor ID</th><th>Vendor Name</th><th>Payable</th><th>Paid</th><th>Due</th></tr></thead>
        <tbody>
          @foreach($topSuppliers as $i => $row)
          <tr>
            <td>{{ $i+1 }}</td>
            <td class="text-blue-600 font-mono text-xs">{{ $row['supplier']->code }}</td>
            <td class="font-medium">{{ $row['supplier']->name }}</td>
            <td>PKR {{ number_format($row['total_payable'],0) }}</td>
            <td class="text-emerald-600">PKR {{ number_format($row['total_paid'],0) }}</td>
            <td class="text-red-600 font-semibold">PKR {{ number_format($row['total_due'],0) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  {{-- Top Customers --}}
  <div class="card">
    <div class="card-header">
      <h2 class="text-sm font-semibold text-gray-700">Top Customers</h2>
      <a href="{{ route('customers.index') }}" class="text-xs text-blue-600 hover:underline">View All</a>
    </div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>#</th><th>Customer ID</th><th>Customer Name</th><th>Payable</th><th>Paid</th><th>Due</th></tr></thead>
        <tbody>
          @foreach($topCustomers as $i => $row)
          <tr>
            <td>{{ $i+1 }}</td>
            <td class="text-blue-600 font-mono text-xs">{{ $row['customer']->code }}</td>
            <td class="font-medium">{{ $row['customer']->name }}</td>
            <td>PKR {{ number_format($row['total_payable'],0) }}</td>
            <td class="text-emerald-600">PKR {{ number_format($row['total_paid'],0) }}</td>
            <td class="text-red-600 font-semibold">PKR {{ number_format($row['total_due'],0) }}</td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const chartData = @json($chartData);
const labels = Object.keys(chartData);
const sales     = labels.map(k => chartData[k].sales);
const purchases = labels.map(k => chartData[k].purchases);
const expenses  = labels.map(k => chartData[k].expenses);
const profit    = labels.map(k => chartData[k].profit);

new Chart(document.getElementById('overviewChart'), {
  type: 'line',
  data: {
    labels,
    datasets: [
      { label:'Sales',     data:sales,     borderColor:'#2563eb', backgroundColor:'rgba(37,99,235,0.08)',  tension:0.4, fill:true, pointRadius:3 },
      { label:'Purchase',  data:purchases, borderColor:'#10b981', backgroundColor:'rgba(16,185,129,0.08)', tension:0.4, fill:true, pointRadius:3 },
      { label:'Expense',   data:expenses,  borderColor:'#f59e0b', backgroundColor:'rgba(245,158,11,0.08)', tension:0.4, fill:true, pointRadius:3 },
      { label:'P&L',       data:profit,    borderColor:'#8b5cf6', backgroundColor:'rgba(139,92,246,0.08)', tension:0.4, fill:true, pointRadius:3 },
    ]
  },
  options: {
    responsive:true, interaction:{ mode:'index', intersect:false },
    plugins:{ legend:{ position:'top', labels:{ usePointStyle:true, padding:16, font:{ size:11 } } } },
    scales:{
      x:{ grid:{ display:false }, ticks:{ font:{ size:11 } } },
      y:{ grid:{ color:'#f1f5f9' }, ticks:{ font:{ size:11 }, callback:v => 'PKR '+v.toLocaleString() } }
    }
  }
});
</script>
@endpush
@endsection
