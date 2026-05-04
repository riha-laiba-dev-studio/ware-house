@extends('layouts.app')
@section('title','Sales')
@section('page-title','Sales Invoices')

@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
  <form class="flex gap-2 flex-wrap">
    <select name="customer_id" class="form-select w-40">
      <option value="">All Customers</option>
      @foreach($customers as $c)<option value="{{ $c->id }}" {{ request('customer_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach
    </select>
    <select name="status" class="form-select w-36">
      <option value="">All Status</option>
      <option value="confirmed" {{ request('status')=='confirmed'?'selected':'' }}>Confirmed</option>
      <option value="pending"   {{ request('status')=='pending'?'selected':'' }}>Pending</option>
    </select>
    <input type="date" name="from" value="{{ request('from') }}" class="form-input w-36">
    <input type="date" name="to"   value="{{ request('to') }}"   class="form-input w-36">
    <button class="btn-primary btn-sm">Filter</button>
    <a href="{{ route('sales.index') }}" class="btn-outline btn-sm">Reset</a>
  </form>
  <a href="{{ route('sales.create') }}" class="btn-primary"><i class="fas fa-plus"></i> New Sale</a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
  <div class="card p-4"><p class="text-xs text-gray-500">Total Sales</p><p class="text-lg font-bold">PKR {{ number_format($sales->sum('total_amount'),0) }}</p></div>
  <div class="card p-4"><p class="text-xs text-gray-500">Total Paid</p><p class="text-lg font-bold text-emerald-600">PKR {{ number_format($sales->sum('paid_amount'),0) }}</p></div>
  <div class="card p-4"><p class="text-xs text-gray-500">Total Due</p><p class="text-lg font-bold text-red-600">PKR {{ number_format($sales->sum('due_amount'),0) }}</p></div>
  <div class="card p-4"><p class="text-xs text-gray-500">Total Invoices</p><p class="text-lg font-bold">{{ $sales->total() }}</p></div>
</div>

<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Reference</th><th>Customer</th><th>Warehouse</th><th>Date</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th>Payment</th><th>Actions</th></tr></thead>
      <tbody>
        @forelse($sales as $s)
        <tr>
          <td><a href="{{ route('sales.show',$s) }}" class="text-blue-600 font-medium hover:underline">{{ $s->reference }}</a></td>
          <td>{{ $s->customer->name }}</td>
          <td>{{ $s->warehouse->name }}</td>
          <td class="text-gray-500">{{ $s->sale_date->format('d M Y') }}</td>
          <td class="font-semibold">PKR {{ number_format($s->total_amount,2) }}</td>
          <td class="text-emerald-600">PKR {{ number_format($s->paid_amount,2) }}</td>
          <td class="text-red-600 font-semibold">PKR {{ number_format($s->due_amount,2) }}</td>
          <td>@php $sm=['pending'=>'badge-warning','confirmed'=>'badge-success','cancelled'=>'badge-danger']; @endphp <span class="badge {{ $sm[$s->status]??'badge-gray' }}">{{ ucfirst($s->status) }}</span></td>
          <td>@php $pm=['unpaid'=>'badge-danger','partial'=>'badge-warning','paid'=>'badge-success']; @endphp <span class="badge {{ $pm[$s->payment_status]??'badge-gray' }}">{{ ucfirst($s->payment_status) }}</span></td>
          <td class="flex gap-1">
            <a href="{{ route('sales.show',$s) }}" class="btn-outline btn-sm"><i class="fas fa-eye"></i></a>
            <a href="{{ route('sales.invoice',$s) }}" class="btn btn-sm text-violet-600 border border-violet-200 hover:bg-violet-50 px-2"><i class="fas fa-print"></i></a>
          </td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center py-8 text-gray-400">No sales found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-4 py-3 border-t border-gray-100">{{ $sales->links() }}</div>
</div>
@endsection
