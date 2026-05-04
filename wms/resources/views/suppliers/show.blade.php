@extends('layouts.app')
@section('title',$supplier->name)
@section('page-title',$supplier->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-4">
    <div class="card">
      <div class="card-header">
        <div><h3 class="font-bold text-gray-800">{{ $supplier->name }}</h3><p class="text-xs font-mono text-blue-600 mt-0.5">{{ $supplier->code }}</p></div>
        <span class="{{ $supplier->is_active ? 'badge badge-success' : 'badge badge-gray' }}">{{ $supplier->is_active ? 'Active' : 'Inactive' }}</span>
      </div>
      <div class="card-body grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
        <div><p class="text-gray-500">Company</p><p class="font-medium">{{ $supplier->company ?: '—' }}</p></div>
        <div><p class="text-gray-500">Phone</p><p class="font-medium">{{ $supplier->phone ?: '—' }}</p></div>
        <div><p class="text-gray-500">Email</p><p class="font-medium">{{ $supplier->email ?: '—' }}</p></div>
        <div><p class="text-gray-500">City</p><p class="font-medium">{{ $supplier->city ?: '—' }}</p></div>
        <div><p class="text-gray-500">Country</p><p class="font-medium">{{ $supplier->country ?: '—' }}</p></div>
        <div><p class="text-gray-500">Opening Balance</p><p class="font-medium">PKR {{ number_format($supplier->opening_balance,2) }}</p></div>
        @if($supplier->notes)<div class="col-span-3"><p class="text-gray-500">Notes</p><p>{{ $supplier->notes }}</p></div>@endif
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Purchase History</h3></div>
      <div class="overflow-x-auto">
        <table class="table">
          <thead><tr><th>Reference</th><th>Warehouse</th><th>Date</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th></tr></thead>
          <tbody>
            @forelse($supplier->purchases->take(10) as $p)
            <tr>
              <td><a href="{{ route('purchases.show',$p) }}" class="text-blue-600 hover:underline">{{ $p->reference }}</a></td>
              <td>{{ $p->warehouse->name }}</td>
              <td>{{ $p->purchase_date->format('d M Y') }}</td>
              <td class="font-semibold">PKR {{ number_format($p->total_amount,2) }}</td>
              <td class="text-emerald-600">PKR {{ number_format($p->paid_amount,2) }}</td>
              <td class="text-red-600">PKR {{ number_format($p->due_amount,2) }}</td>
              <td>@php $sm=['pending'=>'badge-warning','received'=>'badge-success']; @endphp <span class="badge {{ $sm[$p->status]??'badge-gray' }}">{{ ucfirst($p->status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4 text-gray-400">No purchases yet</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="space-y-4">
    <div class="card p-5 space-y-3">
      <div class="flex justify-between text-sm"><span class="text-gray-500">Total Purchases</span><span class="font-bold">PKR {{ number_format($supplier->purchases->sum('total_amount'),2) }}</span></div>
      <div class="flex justify-between text-sm"><span class="text-gray-500">Total Paid</span><span class="font-semibold text-emerald-600">PKR {{ number_format($supplier->purchases->sum('paid_amount'),2) }}</span></div>
      <div class="flex justify-between text-sm border-t pt-3"><span class="font-semibold">Total Due</span><span class="font-bold text-red-600 text-lg">PKR {{ number_format($supplier->purchases->sum('due_amount'),2) }}</span></div>
    </div>
    <a href="{{ route('suppliers.edit',$supplier) }}" class="btn-primary w-full justify-center"><i class="fas fa-pen"></i> Edit Supplier</a>
    <a href="{{ route('purchases.create') }}" class="btn-outline w-full justify-center"><i class="fas fa-cart-flatbed"></i> New Purchase Order</a>
    <a href="{{ route('suppliers.index') }}" class="btn-outline w-full justify-center"><i class="fas fa-arrow-left"></i> Back</a>
  </div>
</div>
@endsection
