@extends('layouts.app')
@section('title','Adjustment')
@section('page-title','Adjustment — '.$inventoryAdjustment->reference)

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
<div class="card">
  <div class="card-header">
    <div><h3 class="font-semibold">{{ $inventoryAdjustment->reference }}</h3></div>
    <span class="badge badge-success">{{ ucfirst($inventoryAdjustment->status) }}</span>
  </div>
  <div class="card-body grid grid-cols-3 gap-4 text-sm">
    <div><p class="text-gray-500">Warehouse</p><p class="font-semibold">{{ $inventoryAdjustment->warehouse->name }}</p></div>
    <div><p class="text-gray-500">Date</p><p class="font-medium">{{ $inventoryAdjustment->adjustment_date->format('d M Y') }}</p></div>
    <div><p class="text-gray-500">Type</p><span class="badge badge-info capitalize">{{ $inventoryAdjustment->type }}</span></div>
    <div><p class="text-gray-500">Created By</p><p class="font-medium">{{ $inventoryAdjustment->creator->name }}</p></div>
    @if($inventoryAdjustment->notes)<div class="col-span-2"><p class="text-gray-500">Notes</p><p>{{ $inventoryAdjustment->notes }}</p></div>@endif
  </div>
</div>
<div class="card">
  <div class="card-header"><h3 class="font-semibold">Adjusted Items</h3></div>
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>#</th><th>Item</th><th>Before</th><th>After</th><th>Difference</th><th>Reason</th></tr></thead>
      <tbody>
        @foreach($inventoryAdjustment->items as $i => $item)
        <tr>
          <td>{{ $i+1 }}</td>
          <td class="font-medium">{{ $item->item->name }}</td>
          <td>{{ number_format($item->current_quantity,2) }}</td>
          <td>{{ number_format($item->adjusted_quantity,2) }}</td>
          <td><span class="{{ $item->difference >= 0 ? 'badge badge-success' : 'badge badge-danger' }}">{{ $item->difference >= 0 ? '+' : '' }}{{ number_format($item->difference,2) }}</span></td>
          <td class="text-gray-500 text-xs">{{ $item->reason ?: '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
<a href="{{ route('inventory-adjustments.index') }}" class="btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
</div>
@endsection
