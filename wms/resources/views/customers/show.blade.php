@extends('layouts.app')
@section('title',$customer->name)
@section('page-title',$customer->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-4">
    <div class="card">
      <div class="card-header">
        <div><h3 class="font-bold">{{ $customer->name }}</h3><p class="text-xs font-mono text-blue-600 mt-0.5">{{ $customer->code }}</p></div>
        <span class="{{ $customer->is_active ? 'badge badge-success' : 'badge badge-gray' }}">{{ $customer->is_active ? 'Active' : 'Inactive' }}</span>
      </div>
      <div class="card-body grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
        <div><p class="text-gray-500">Company</p><p class="font-medium">{{ $customer->company ?: '—' }}</p></div>
        <div><p class="text-gray-500">Phone</p><p class="font-medium">{{ $customer->phone ?: '—' }}</p></div>
        <div><p class="text-gray-500">Email</p><p class="font-medium">{{ $customer->email ?: '—' }}</p></div>
        <div><p class="text-gray-500">City</p><p class="font-medium">{{ $customer->city ?: '—' }}</p></div>
        <div><p class="text-gray-500">Credit Limit</p><p class="font-medium">PKR {{ number_format($customer->credit_limit,2) }}</p></div>
        <div><p class="text-gray-500">Opening Balance</p><p class="font-medium">PKR {{ number_format($customer->opening_balance,2) }}</p></div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Sale History</h3></div>
      <div class="overflow-x-auto">
        <table class="table">
          <thead><tr><th>Reference</th><th>Warehouse</th><th>Date</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th></tr></thead>
          <tbody>
            @forelse($customer->sales->take(10) as $s)
            <tr>
              <td><a href="{{ route('sales.show',$s) }}" class="text-blue-600 hover:underline">{{ $s->reference }}</a></td>
              <td>{{ $s->warehouse->name }}</td>
              <td>{{ $s->sale_date->format('d M Y') }}</td>
              <td class="font-semibold">PKR {{ number_format($s->total_amount,2) }}</td>
              <td class="text-emerald-600">PKR {{ number_format($s->paid_amount,2) }}</td>
              <td class="text-red-600">PKR {{ number_format($s->due_amount,2) }}</td>
              <td>@php $pm=['unpaid'=>'badge-danger','partial'=>'badge-warning','paid'=>'badge-success']; @endphp <span class="badge {{ $pm[$s->payment_status]??'badge-gray' }}">{{ ucfirst($s->payment_status) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-4 text-gray-400">No sales yet</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="space-y-4">
    <div class="card p-5 space-y-3">
      <div class="flex justify-between text-sm"><span class="text-gray-500">Total Sales</span><span class="font-bold">PKR {{ number_format($customer->sales->sum('total_amount'),2) }}</span></div>
      <div class="flex justify-between text-sm"><span class="text-gray-500">Total Paid</span><span class="font-semibold text-emerald-600">PKR {{ number_format($customer->sales->sum('paid_amount'),2) }}</span></div>
      <div class="flex justify-between text-sm border-t pt-3"><span class="font-semibold">Total Due</span><span class="font-bold text-red-600 text-lg">PKR {{ number_format($customer->sales->sum('due_amount'),2) }}</span></div>
    </div>
    <a href="{{ route('customers.edit',$customer) }}" class="btn-primary w-full justify-center"><i class="fas fa-pen"></i> Edit Customer</a>
    <a href="{{ route('sales.create') }}" class="btn-outline w-full justify-center"><i class="fas fa-receipt"></i> New Sale</a>
    <a href="{{ route('customers.index') }}" class="btn-outline w-full justify-center"><i class="fas fa-arrow-left"></i> Back</a>
  </div>
</div>
@endsection
