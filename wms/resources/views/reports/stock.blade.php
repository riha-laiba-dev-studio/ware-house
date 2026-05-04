@extends('layouts.app')
@section('title','Stock Report')
@section('page-title','Stock Report')

@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
  <form class="flex gap-2 flex-wrap">
    <select name="warehouse_id" class="form-select w-40">
      <option value="">All Warehouses</option>
      @foreach($warehouses as $w)<option value="{{ $w->id }}" {{ request('warehouse_id')==$w->id?'selected':'' }}>{{ $w->name }}</option>@endforeach
    </select>
    <button class="btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
    <a href="{{ route('reports.stock') }}" class="btn-outline btn-sm">Reset</a>
  </form>
  <div class="flex items-center gap-2">
    <a href="{{ route('reports.stock.csv') }}?{{ request()->getQueryString() }}" class="btn-outline btn-sm">
      <i class="fas fa-file-csv text-emerald-600"></i> CSV
    </a>
    <a href="{{ route('reports.stock.pdf') }}?{{ request()->getQueryString() }}" class="btn-outline btn-sm" target="_blank">
      <i class="fas fa-file-pdf text-red-500"></i> PDF
    </a>
    <button onclick="window.print()" class="btn-outline btn-sm"><i class="fas fa-print text-blue-500"></i> Print</button>
  </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-5">
  <div class="card p-4 flex items-center gap-3">
    <div class="w-10 h-10 bg-violet-100 text-violet-600 rounded-xl flex items-center justify-center"><i class="fas fa-boxes-stacked"></i></div>
    <div><p class="text-xs text-gray-500">Total SKUs</p><p class="text-xl font-bold text-gray-800">{{ $stock->count() }}</p></div>
  </div>
  <div class="card p-4 flex items-center gap-3">
    <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center"><i class="fas fa-triangle-exclamation"></i></div>
    <div><p class="text-xs text-gray-500">Low Stock</p><p class="text-xl font-bold text-red-600">{{ $lowStock }}</p></div>
  </div>
  <div class="card p-4 flex items-center gap-3 lg:col-span-1 col-span-2">
    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center"><i class="fas fa-dollar-sign"></i></div>
    <div><p class="text-xs text-gray-500">Total Stock Value</p><p class="text-xl font-bold text-emerald-700">PKR {{ number_format($totalValue,0) }}</p></div>
  </div>
</div>

<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Item</th><th>SKU</th><th>Category</th><th>Warehouse</th><th>Unit</th><th>Qty</th><th>Cost Price</th><th>Stock Value</th><th>Alert</th></tr></thead>
      <tbody>
        @forelse($stock as $s)
        <tr>
          <td class="font-medium">{{ $s->item->name }}</td>
          <td class="font-mono text-xs text-blue-600">{{ $s->item->sku }}</td>
          <td>{{ $s->item->category->name }}</td>
          <td>{{ $s->warehouse->name }}</td>
          <td>{{ $s->item->unit->symbol }}</td>
          <td class="{{ $s->quantity <= $s->item->alert_quantity ? 'font-bold text-red-600' : 'font-semibold text-gray-800' }}">{{ number_format($s->quantity,2) }}</td>
          <td>PKR {{ number_format($s->item->purchase_price,2) }}</td>
          <td class="font-semibold text-emerald-600">PKR {{ number_format($s->stock_value,2) }}</td>
          <td>@if($s->quantity <= $s->item->alert_quantity)<span class="badge badge-danger">Low</span>@else<span class="badge badge-success">OK</span>@endif</td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center py-8 text-gray-400">No stock data</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
