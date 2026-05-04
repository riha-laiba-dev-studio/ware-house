@extends('layouts.app')
@section('title','Suppliers')
@section('page-title','Manage Vendors / Suppliers')

@section('content')
<div class="flex items-center justify-between mb-5">
  <form class="flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search suppliers..." class="form-input w-56">
    <button class="btn-primary btn-sm">Search</button>
    <a href="{{ route('suppliers.index') }}" class="btn-outline btn-sm">Reset</a>
  </form>
  <a href="{{ route('suppliers.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Add Supplier</a>
</div>
<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Code</th><th>Supplier Name</th><th>Phone</th><th>City</th><th>Total Purchase</th><th>Paid</th><th>Due</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($suppliers as $s)
        <tr>
          <td class="font-mono text-xs text-blue-600">{{ $s->code }}</td>
          <td><p class="font-medium">{{ $s->name }}</p><p class="text-xs text-gray-400">{{ $s->company }}</p></td>
          <td>{{ $s->phone ?: '—' }}</td>
          <td>{{ $s->city ?: '—' }}</td>
          <td>PKR {{ number_format($s->purchases_sum_total_amount ?? 0,0) }}</td>
          <td class="text-emerald-600">PKR {{ number_format($s->purchases_sum_paid_amount ?? 0,0) }}</td>
          <td class="text-red-600 font-semibold">PKR {{ number_format(($s->purchases_sum_total_amount??0)-($s->purchases_sum_paid_amount??0),0) }}</td>
          <td><span class="{{ $s->is_active ? 'badge badge-success' : 'badge badge-gray' }}">{{ $s->is_active ? 'Active' : 'Inactive' }}</span></td>
          <td class="flex gap-1">
            <a href="{{ route('suppliers.show',$s) }}" class="btn-outline btn-sm"><i class="fas fa-eye"></i></a>
            <a href="{{ route('suppliers.edit',$s) }}" class="btn btn-sm text-amber-600 border border-amber-200 hover:bg-amber-50 px-2"><i class="fas fa-pen"></i></a>
            <form method="POST" action="{{ route('suppliers.destroy',$s) }}">
              @csrf @method('DELETE')
              <button data-confirm="Delete supplier?" class="btn btn-sm text-red-600 border border-red-200 hover:bg-red-50 px-2"><i class="fas fa-trash"></i></button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center py-8 text-gray-400">No suppliers found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-4 py-3 border-t border-gray-100">{{ $suppliers->links() }}</div>
</div>
@endsection
