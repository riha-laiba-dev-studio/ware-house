@extends('layouts.app')
@section('title','Sales Report')
@section('page-title','Sales Report')

@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
  <form class="flex gap-2 flex-wrap">
    <div><label class="form-label text-xs">From</label><input type="date" name="from" value="{{ $from }}" class="form-input"></div>
    <div><label class="form-label text-xs">To</label><input type="date" name="to" value="{{ $to }}" class="form-input"></div>
    <div><label class="form-label text-xs">Warehouse</label>
      <select name="warehouse_id" class="form-select w-36">
        <option value="">All</option>
        @foreach($warehouses as $w)<option value="{{ $w->id }}" {{ request('warehouse_id')==$w->id?'selected':'' }}>{{ $w->name }}</option>@endforeach
      </select>
    </div>
    <div><label class="form-label text-xs">Customer</label>
      <select name="customer_id" class="form-select w-36">
        <option value="">All</option>
        @foreach($customers as $c)<option value="{{ $c->id }}" {{ request('customer_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach
      </select>
    </div>
    <div class="flex items-end"><button class="btn-primary btn-sm">Generate</button></div>
  </form>
</div>

<div class="card p-4 mb-4 flex justify-between items-center">
  <div><p class="text-xs text-gray-500">Total Sales Revenue</p><p class="text-2xl font-bold text-blue-700">PKR {{ number_format($total,2) }}</p></div>
  <div class="text-sm text-gray-400">{{ $sales->count() }} invoices</div>
</div>

<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Reference</th><th>Customer</th><th>Date</th><th>Items</th><th>Subtotal</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th></tr></thead>
      <tbody>
        @forelse($sales as $s)
        <tr>
          <td><a href="{{ route('sales.show',$s) }}" class="text-blue-600 hover:underline font-medium">{{ $s->reference }}</a></td>
          <td>{{ $s->customer->name }}</td>
          <td>{{ $s->sale_date->format('d M Y') }}</td>
          <td>{{ $s->items->count() }}</td>
          <td>PKR {{ number_format($s->subtotal,2) }}</td>
          <td class="font-bold">PKR {{ number_format($s->total_amount,2) }}</td>
          <td class="text-emerald-600">PKR {{ number_format($s->paid_amount,2) }}</td>
          <td class="text-red-600">PKR {{ number_format($s->due_amount,2) }}</td>
          <td>@php $pm=['unpaid'=>'badge-danger','partial'=>'badge-warning','paid'=>'badge-success']; @endphp <span class="badge {{ $pm[$s->payment_status]??'badge-gray' }}">{{ ucfirst($s->payment_status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center py-8 text-gray-400">No sales in this period</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
