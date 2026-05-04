@extends('layouts.app')
@section('title','Customers')
@section('page-title','Manage Customers')

@section('content')
<div class="flex items-center justify-between mb-5">
  <form class="flex gap-2">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customers..." class="form-input w-56">
    <button class="btn-primary btn-sm">Search</button>
    <a href="{{ route('customers.index') }}" class="btn-outline btn-sm">Reset</a>
  </form>
  <a href="{{ route('customers.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Add Customer</a>
</div>
<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Code</th><th>Customer Name</th><th>Phone</th><th>City</th><th>Total Sales</th><th>Paid</th><th>Due</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($customers as $c)
        <tr>
          <td class="font-mono text-xs text-blue-600">{{ $c->code }}</td>
          <td><p class="font-medium">{{ $c->name }}</p><p class="text-xs text-gray-400">{{ $c->company }}</p></td>
          <td>{{ $c->phone ?: '—' }}</td>
          <td>{{ $c->city ?: '—' }}</td>
          <td>PKR {{ number_format($c->sales_sum_total_amount ?? 0,0) }}</td>
          <td class="text-emerald-600">PKR {{ number_format($c->sales_sum_paid_amount ?? 0,0) }}</td>
          <td class="text-red-600 font-semibold">PKR {{ number_format($c->sales_sum_due_amount ?? 0,0) }}</td>
          <td><span class="{{ $c->is_active ? 'badge badge-success' : 'badge badge-gray' }}">{{ $c->is_active ? 'Active' : 'Inactive' }}</span></td>
          <td class="flex gap-1">
            <a href="{{ route('customers.show',$c) }}" class="btn-outline btn-sm"><i class="fas fa-eye"></i></a>
            <a href="{{ route('customers.edit',$c) }}" class="btn btn-sm text-amber-600 border border-amber-200 hover:bg-amber-50 px-2"><i class="fas fa-pen"></i></a>
            <form method="POST" action="{{ route('customers.destroy',$c) }}">@csrf @method('DELETE')<button data-confirm="Delete customer?" class="btn btn-sm text-red-600 border border-red-200 hover:bg-red-50 px-2"><i class="fas fa-trash"></i></button></form>
          </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center py-8 text-gray-400">No customers found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-4 py-3 border-t border-gray-100">{{ $customers->links() }}</div>
</div>
@endsection
