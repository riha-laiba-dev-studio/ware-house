@extends('layouts.app')
@section('title','Purchase Return — '.$purchaseReturn->reference)
@section('page-title','Purchase Return Detail')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2 space-y-5">
    <div class="card">
      <div class="card-header">
        <div>
          <h3 class="font-semibold text-gray-800">{{ $purchaseReturn->reference }}</h3>
          <p class="text-xs text-gray-400 mt-0.5">{{ $purchaseReturn->return_date->format('d M Y') }}</p>
        </div>
        <span class="badge {{ $purchaseReturn->status==='approved'?'badge-success':'badge-warning' }}">{{ ucfirst($purchaseReturn->status) }}</span>
      </div>
      <div class="card-body grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
        <div><p class="text-gray-500">Supplier</p><p class="font-medium">{{ $purchaseReturn->supplier->name }}</p></div>
        <div><p class="text-gray-500">Warehouse</p><p class="font-medium">{{ $purchaseReturn->warehouse->name }}</p></div>
        <div><p class="text-gray-500">Original PO</p><a href="{{ route('purchases.show',$purchaseReturn->purchase) }}" class="font-medium text-blue-600 hover:underline">{{ $purchaseReturn->purchase->reference }}</a></div>
        <div><p class="text-gray-500">Created By</p><p class="font-medium">{{ $purchaseReturn->creator->name }}</p></div>
        @if($purchaseReturn->reason)<div class="md:col-span-2"><p class="text-gray-500">Reason</p><p class="font-medium">{{ $purchaseReturn->reason }}</p></div>@endif
      </div>
    </div>
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Returned Items</h3></div>
      <div class="overflow-x-auto">
        <table class="table">
          <thead><tr><th>#</th><th>Item</th><th>Unit</th><th>Qty Returned</th><th>Unit Cost</th><th>Subtotal</th></tr></thead>
          <tbody>
            @foreach($purchaseReturn->items as $i => $item)
            <tr>
              <td>{{ $i+1 }}</td>
              <td><p class="font-medium">{{ $item->item->name }}</p><p class="text-xs text-gray-400 font-mono">{{ $item->item->sku }}</p></td>
              <td>{{ $item->item->unit->symbol }}</td>
              <td class="text-orange-600 font-semibold">-{{ number_format($item->quantity,2) }}</td>
              <td>PKR {{ number_format($item->unit_cost,2) }}</td>
              <td class="font-bold">PKR {{ number_format($item->subtotal,2) }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot class="bg-gray-50"><tr><td colspan="5" class="text-right font-semibold px-4 py-3">Total Returned:</td><td class="px-4 py-3 font-bold text-orange-700 text-lg">PKR {{ number_format($purchaseReturn->total_amount,2) }}</td></tr></tfoot>
        </table>
      </div>
    </div>
  </div>
  <div class="space-y-4">
    <div class="card p-5 bg-orange-50 border border-orange-200">
      <p class="text-xs text-orange-700 font-semibold mb-1">STOCK DEDUCTED</p>
      <p class="text-2xl font-bold text-orange-800">PKR {{ number_format($purchaseReturn->total_amount,2) }}</p>
      <p class="text-xs text-orange-600 mt-1">{{ $purchaseReturn->items->sum('quantity') }} units returned to supplier</p>
    </div>
    <a href="{{ route('purchase-returns.index') }}" class="btn-outline w-full justify-center"><i class="fas fa-arrow-left"></i> Back to Returns</a>
  </div>
</div>
@endsection
