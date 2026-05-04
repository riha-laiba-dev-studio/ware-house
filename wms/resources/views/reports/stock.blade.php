@extends('layouts.app')
@section('title','Stock Report')
@section('page-title','Stock Report')

@section('content')
<div class="flex items-center justify-between mb-5">
  <form class="flex gap-2">
    <select name="warehouse_id" class="form-select w-40">
      <option value="">All Warehouses</option>
      @foreach($warehouses as $w)<option value="{{ $w->id }}" {{ request('warehouse_id')==$w->id?'selected':'' }}>{{ $w->name }}</option>@endforeach
    </select>
    <button class="btn-primary btn-sm">Filter</button>
    <a href="{{ route('reports.stock') }}" class="btn-outline btn-sm">Reset</a>
  </form>
</div>

<div class="card p-4 mb-4 flex justify-between items-center">
  <div><p class="text-xs text-gray-500">Total Stock Value</p><p class="text-2xl font-bold text-blue-700">PKR {{ number_format($totalValue,2) }}</p></div>
  <div class="text-sm text-gray-400">{{ $stock->count() }} item types</div>
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
