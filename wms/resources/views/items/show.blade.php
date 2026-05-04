@extends('layouts.app')
@section('title',$item->name)
@section('page-title',$item->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-4">
    <div class="card">
      <div class="card-header">
        <div><h3 class="font-bold text-gray-800">{{ $item->name }}</h3><p class="text-xs font-mono text-blue-600 mt-0.5">{{ $item->sku }}</p></div>
        <span class="{{ $item->is_active ? 'badge badge-success' : 'badge badge-gray' }}">{{ $item->is_active ? 'Active' : 'Inactive' }}</span>
      </div>
      <div class="card-body grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
        <div><p class="text-gray-500">Category</p><p class="font-medium">{{ $item->category->name }}</p></div>
        <div><p class="text-gray-500">Unit</p><p class="font-medium">{{ $item->unit->name }} ({{ $item->unit->symbol }})</p></div>
        <div><p class="text-gray-500">Brand</p><p class="font-medium">{{ $item->brand?->name ?? '—' }}</p></div>
        <div><p class="text-gray-500">Purchase Price</p><p class="font-bold text-gray-800">PKR {{ number_format($item->purchase_price,2) }}</p></div>
        <div><p class="text-gray-500">Selling Price</p><p class="font-bold text-blue-700">PKR {{ number_format($item->selling_price,2) }}</p></div>
        <div><p class="text-gray-500">Alert Quantity</p><p class="font-medium">{{ $item->alert_quantity }}</p></div>
        @if($item->description)<div class="col-span-3"><p class="text-gray-500">Description</p><p>{{ $item->description }}</p></div>@endif
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Stock by Warehouse</h3></div>
      <div class="overflow-x-auto">
        <table class="table">
          <thead><tr><th>Warehouse</th><th>Quantity</th><th>Reserved</th><th>Available</th><th>Alert</th></tr></thead>
          <tbody>
            @forelse($item->inventory as $inv)
            <tr>
              <td class="font-medium">{{ $inv->warehouse->name }}</td>
              <td class="{{ $inv->quantity <= $item->alert_quantity ? 'text-red-600 font-bold' : 'font-semibold' }}">{{ number_format($inv->quantity,2) }}</td>
              <td class="text-amber-600">{{ number_format($inv->reserved_quantity,2) }}</td>
              <td class="text-emerald-600 font-semibold">{{ number_format($inv->available_quantity,2) }}</td>
              <td>@if($inv->quantity <= $item->alert_quantity)<span class="badge badge-danger">Low</span>@else<span class="badge badge-success">OK</span>@endif</td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-4 text-gray-400">No stock records</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="space-y-4">
    <div class="card p-4 text-center">
      <img src="{{ $item->image_url }}" class="w-full max-h-48 object-cover rounded-lg mx-auto mb-3">
      <p class="text-3xl font-bold text-gray-800">{{ number_format($item->total_stock,2) }}</p>
      <p class="text-sm text-gray-500">Total Stock (All Warehouses)</p>
      @if($item->isLowStock())<span class="badge badge-danger mt-2">Low Stock Alert</span>@endif
    </div>
    <a href="{{ route('items.edit',$item) }}" class="btn-primary w-full justify-center"><i class="fas fa-pen"></i> Edit Item</a>
    <a href="{{ route('items.index') }}" class="btn-outline w-full justify-center"><i class="fas fa-arrow-left"></i> Back</a>
  </div>
</div>
@endsection
