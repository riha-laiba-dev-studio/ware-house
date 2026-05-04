@extends('layouts.app')
@section('title','Stock Transfers')
@section('page-title','Stock Transfers')

@section('content')
<div class="flex justify-end mb-5">
  <a href="{{ route('stock-transfers.create') }}" class="btn-primary"><i class="fas fa-plus"></i> New Transfer</a>
</div>
<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Reference</th><th>From</th><th>To</th><th>Date</th><th>Items</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($transfers as $t)
        <tr>
          <td class="font-medium text-blue-600"><a href="{{ route('stock-transfers.show',$t) }}">{{ $t->reference }}</a></td>
          <td><span class="badge badge-info">{{ $t->fromWarehouse->name }}</span></td>
          <td><span class="badge badge-success">{{ $t->toWarehouse->name }}</span></td>
          <td class="text-gray-500">{{ $t->transfer_date->format('d M Y') }}</td>
          <td>{{ $t->items->count() }} items</td>
          <td>@php $sm=['pending'=>'badge-warning','completed'=>'badge-success','cancelled'=>'badge-danger']; @endphp <span class="badge {{ $sm[$t->status]??'badge-gray' }}">{{ ucfirst($t->status) }}</span></td>
          <td class="flex gap-1">
            <a href="{{ route('stock-transfers.show',$t) }}" class="btn-outline btn-sm"><i class="fas fa-eye"></i></a>
            @if($t->status === 'pending')
            <form method="POST" action="{{ route('stock-transfers.approve',$t) }}">@csrf
              <button data-confirm="Approve this transfer?" class="btn-success btn-sm"><i class="fas fa-check"></i> Approve</button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center py-8 text-gray-400">No transfers found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-4 py-3 border-t border-gray-100">{{ $transfers->links() }}</div>
</div>
@endsection
