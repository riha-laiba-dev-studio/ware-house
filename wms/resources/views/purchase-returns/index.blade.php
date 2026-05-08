@extends('layouts.app')
@section('title','Purchase Returns')
@section('page-title','Purchase Returns')

@section('content')
<div class="flex items-center justify-between mb-5">
  <p class="text-sm text-gray-500">Items returned to suppliers stock is automatically deducted.</p>
  <a href="{{ route('purchase-returns.create') }}" class="btn-primary"><i class="fas fa-plus"></i> New Purchase Return</a>
</div>

<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead>
        <tr><th>Reference</th><th>PO Ref</th><th>Supplier</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th>Action</th></tr>
      </thead>
      <tbody>
        @forelse($returns as $r)
        <tr>
          <td class="font-mono text-xs text-orange-700 font-semibold">{{ $r->reference }}</td>
          <td><a href="{{ route('purchases.show',$r->purchase) }}" class="text-blue-600 hover:underline text-xs">{{ $r->purchase->reference }}</a></td>
          <td class="font-medium">{{ $r->supplier->name }}</td>
          <td>{{ $r->return_date->format('d M Y') }}</td>
          <td class="text-center"><span class="badge badge-info">{{ $r->items->count() }}</span></td>
          <td class="font-bold">PKR {{ number_format($r->total_amount,2) }}</td>
          <td><span class="badge {{ $r->status==='approved'?'badge-success':'badge-warning' }}">{{ ucfirst($r->status) }}</span></td>
          <td><a href="{{ route('purchase-returns.show',$r) }}" class="btn-outline btn-sm"><i class="fas fa-eye"></i></a></td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center py-10 text-gray-400"><i class="fas fa-rotate-right text-3xl mb-2 block opacity-30"></i>No purchase returns yet</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($returns->hasPages())
  <div class="px-5 py-3 border-t border-gray-100">{{ $returns->links() }}</div>
  @endif
</div>
@endsection
