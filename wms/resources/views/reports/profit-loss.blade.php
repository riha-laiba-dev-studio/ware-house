@extends('layouts.app')
@section('title','P&L Report')
@section('page-title','Profit & Loss Report')

@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
  <form class="flex gap-2 flex-wrap">
    <div><label class="form-label text-xs">From</label><input type="date" name="from" value="{{ $from }}" class="form-input"></div>
    <div><label class="form-label text-xs">To</label><input type="date" name="to" value="{{ $to }}" class="form-input"></div>
    <div class="flex items-end gap-2">
      <button class="btn-primary btn-sm"><i class="fas fa-chart-line"></i> Generate</button>
      <a href="{{ route('reports.profit-loss') }}" class="btn-outline btn-sm">Reset</a>
    </div>
  </form>
  <div class="flex items-center gap-2">
    <a href="{{ route('reports.profit-loss.pdf') }}?from={{ $from }}&to={{ $to }}" class="btn-outline btn-sm" target="_blank">
      <i class="fas fa-file-pdf text-red-500"></i> PDF
    </a>
    <button onclick="window.print()" class="btn-outline btn-sm"><i class="fas fa-print text-blue-500"></i> Print</button>
  </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
  <div class="stat-card"><div class="stat-icon bg-blue-100 text-blue-600"><i class="fas fa-receipt"></i></div><div><p class="text-xs text-gray-500">Total Sales</p><p class="text-2xl font-bold text-gray-800">PKR {{ number_format($data['sales'],2) }}</p></div></div>
  <div class="stat-card"><div class="stat-icon bg-red-100 text-red-600"><i class="fas fa-boxes"></i></div><div><p class="text-xs text-gray-500">Cost of Goods Sold</p><p class="text-2xl font-bold text-gray-800">PKR {{ number_format($data['costOfGoods'],2) }}</p></div></div>
  <div class="stat-card"><div class="stat-icon bg-emerald-100 text-emerald-600"><i class="fas fa-chart-line"></i></div><div><p class="text-xs text-gray-500">Gross Profit</p><p class="text-2xl font-bold {{ $data['grossProfit'] >= 0 ? 'text-emerald-700' : 'text-red-600' }}">PKR {{ number_format($data['grossProfit'],2) }}</p></div></div>
  <div class="stat-card"><div class="stat-icon bg-amber-100 text-amber-600"><i class="fas fa-money-bill-wave"></i></div><div><p class="text-xs text-gray-500">Total Expenses</p><p class="text-2xl font-bold text-gray-800">PKR {{ number_format($data['expenses'],2) }}</p></div></div>
  <div class="stat-card lg:col-span-2"><div class="stat-icon {{ $data['netProfit'] >= 0 ? 'bg-violet-100 text-violet-600' : 'bg-red-100 text-red-600' }}"><i class="fas fa-scale-balanced"></i></div><div><p class="text-xs text-gray-500">Net Profit / (Loss)</p><p class="text-3xl font-bold {{ $data['netProfit'] >= 0 ? 'text-violet-700' : 'text-red-600' }}">PKR {{ number_format($data['netProfit'],2) }}</p><p class="text-xs text-gray-400 mt-1">Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p></div></div>
</div>

<div class="card">
  <div class="card-header"><h3 class="font-semibold text-gray-700">Monthly Trend (Last 6 Months)</h3></div>
  <div class="card-body"><canvas id="plChart" height="120"></canvas></div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const cd = @json($chartData);
const labels = Object.keys(cd);
new Chart(document.getElementById('plChart'), {
  type:'bar',
  data:{ labels, datasets:[
    {label:'Sales',data:labels.map(k=>cd[k].sales),backgroundColor:'rgba(37,99,235,0.7)',borderRadius:4},
    {label:'Expenses',data:labels.map(k=>cd[k].expenses),backgroundColor:'rgba(245,158,11,0.7)',borderRadius:4},
    {label:'Net Profit',data:labels.map(k=>cd[k].profit),backgroundColor:'rgba(139,92,246,0.7)',borderRadius:4},
  ]},
  options:{responsive:true,plugins:{legend:{position:'top'}},scales:{y:{grid:{color:'#f1f5f9'},ticks:{callback:v=>'PKR '+v.toLocaleString()}}}}
});
</script>
@endpush
@endsection
