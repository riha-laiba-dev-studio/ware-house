@extends('layouts.app')
@section('title','Inventory Adjustments')
@section('page-title','Stock Listing / Adjustments')

@section('content')
<div class="flex justify-end mb-5">
  <a href="{{ route('inventory-adjustments.create') }}" class="btn-primary"><i class="fas fa-plus"></i> New Adjustment</a>
</div>
<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Reference</th><th>Warehouse</th><th>Date</th><th>Type</th><th>Items</th><th>Status</th><th>Created By</th><th>Action</th></tr></thead>
      <tbody>
        @forelse($adjustments as $a)
        <tr>
          <td class="font-medium text-blue-600"><a href="{{ route('inventory-adjustments.show',$a) }}">{{ $a->reference }}</a></td>
          <td>{{ $a->warehouse->name }}</td>
          <td>{{ $a->adjustment_date->format('d M Y') }}</td>
          <td><span class="badge badge-info capitalize">{{ $a->type }}</span></td>
          <td>{{ $a->items->count() }}</td>
          <td><span class="{{ $a->status==='approved' ? 'badge badge-success' : 'badge badge-warning' }}">{{ ucfirst($a->status) }}</span></td>
          <td>{{ $a->creator->name }}</td>
          <td><a href="{{ route('inventory-adjustments.show',$a) }}" class="btn-outline btn-sm"><i class="fas fa-eye"></i></a></td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center py-8 text-gray-400">No adjustments found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-4 py-3 border-t">{{ $adjustments->links() }}</div>
</div>
@endsection
