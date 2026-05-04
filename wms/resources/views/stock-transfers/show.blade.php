@extends('layouts.app')
@section('title','Transfer '.$stockTransfer->reference)
@section('page-title','Stock Transfer — '.$stockTransfer->reference)

@section('content')
<div class="max-w-3xl mx-auto space-y-4">
<div class="card">
  <div class="card-header">
    <div><h3 class="font-semibold">{{ $stockTransfer->reference }}</h3><p class="text-xs text-gray-400">{{ $stockTransfer->transfer_date->format('d M Y') }}</p></div>
    @php $sm=['pending'=>'badge-warning','completed'=>'badge-success','cancelled'=>'badge-danger']; @endphp
    <span class="badge {{ $sm[$stockTransfer->status]??'badge-gray' }} text-sm">{{ ucfirst($stockTransfer->status) }}</span>
  </div>
  <div class="card-body grid grid-cols-3 gap-4 text-sm">
    <div><p class="text-gray-500">From Warehouse</p><p class="font-semibold text-red-600">{{ $stockTransfer->fromWarehouse->name }}</p></div>
    <div class="text-center"><i class="fas fa-arrow-right text-2xl text-gray-400"></i></div>
    <div><p class="text-gray-500">To Warehouse</p><p class="font-semibold text-emerald-600">{{ $stockTransfer->toWarehouse->name }}</p></div>
    <div><p class="text-gray-500">Created By</p><p class="font-medium">{{ $stockTransfer->creator->name }}</p></div>
    @if($stockTransfer->notes)<div class="col-span-2"><p class="text-gray-500">Notes</p><p>{{ $stockTransfer->notes }}</p></div>@endif
  </div>
</div>

<div class="card">
  <div class="card-header"><h3 class="font-semibold text-gray-700">Transfer Items</h3></div>
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>#</th><th>Item</th><th>SKU</th><th>Quantity</th><th>Unit Cost</th></tr></thead>
      <tbody>
        @foreach($stockTransfer->items as $i => $item)
        <tr>
          <td>{{ $i+1 }}</td>
          <td class="font-medium">{{ $item->item->name }}</td>
          <td class="text-xs font-mono text-blue-600">{{ $item->item->sku }}</td>
          <td class="font-semibold">{{ number_format($item->quantity,2) }}</td>
          <td>PKR {{ number_format($item->unit_cost,2) }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

<div class="flex gap-3">
  @if($stockTransfer->status === 'pending')
  <form method="POST" action="{{ route('stock-transfers.approve',$stockTransfer) }}">@csrf
    <button data-confirm="Approve this transfer? Stock will be moved." class="btn-success"><i class="fas fa-check-circle"></i> Approve & Move Stock</button>
  </form>
  @endif
  <a href="{{ route('stock-transfers.index') }}" class="btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
</div>
</div>
@endsection
