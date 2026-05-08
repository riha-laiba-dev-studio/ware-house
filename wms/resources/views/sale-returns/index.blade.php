@extends('layouts.app')
@section('title','Sale Returns')
@section('page-title','Sale Returns')

@section('content')
<div class="flex items-center justify-between mb-5">
  <p class="text-sm text-gray-500">Returned items from customers stock is automatically restored.</p>
  <a href="{{ route('sale-returns.create') }}" class="btn-primary"><i class="fas fa-plus"></i> New Sale Return</a>
</div>

<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead>
        <tr><th>Reference</th><th>Sale Ref</th><th>Customer</th><th>Date</th><th>Items</th><th>Total</th><th>Status</th><th>Action</th></tr>
      </thead>
      <tbody>
        @forelse($returns as $r)
        <tr>
          <td class="font-mono text-xs text-teal-700 font-semibold">{{ $r->reference }}</td>
          <td><a href="{{ route('sales.show',$r->sale) }}" class="text-blue-600 hover:underline text-xs">{{ $r->sale->reference }}</a></td>
          <td class="font-medium">{{ $r->customer->name }}</td>
          <td>{{ $r->return_date->format('d M Y') }}</td>
          <td class="text-center"><span class="badge badge-info">{{ $r->items->count() }}</span></td>
          <td class="font-bold">PKR {{ number_format($r->total_amount,2) }}</td>
          <td><span class="badge {{ $r->status==='approved'?'badge-success':'badge-warning' }}">{{ ucfirst($r->status) }}</span></td>
          <td><a href="{{ route('sale-returns.show',$r) }}" class="btn-outline btn-sm"><i class="fas fa-eye"></i></a></td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center py-10 text-gray-400"><i class="fas fa-rotate-left text-3xl mb-2 block opacity-30"></i>No sale returns yet</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($returns->hasPages())
  <div class="px-5 py-3 border-t border-gray-100">{{ $returns->links() }}</div>
  @endif
</div>
@endsection
