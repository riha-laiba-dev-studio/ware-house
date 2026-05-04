@extends('layouts.app')
@section('title','Purchase Report')
@section('page-title','Purchase Report')

@section('content')
<div class="flex items-center justify-between mb-5 flex-wrap gap-3">
  <form class="flex gap-2 flex-wrap">
    <div><label class="form-label text-xs">From</label><input type="date" name="from" value="{{ $from }}" class="form-input"></div>
    <div><label class="form-label text-xs">To</label><input type="date" name="to" value="{{ $to }}" class="form-input"></div>
    <div><label class="form-label text-xs">Supplier</label>
      <select name="supplier_id" class="form-select w-36">
        <option value="">All</option>
        @foreach($suppliers as $s)<option value="{{ $s->id }}" {{ request('supplier_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach
      </select>
    </div>
    <div class="flex items-end"><button class="btn-primary btn-sm">Generate</button></div>
  </form>
</div>

<div class="card p-4 mb-4 flex justify-between items-center">
  <div><p class="text-xs text-gray-500">Total Purchase Amount</p><p class="text-2xl font-bold text-gray-800">PKR {{ number_format($total,2) }}</p></div>
  <div class="text-sm text-gray-400">{{ $purchases->count() }} orders</div>
</div>

<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Reference</th><th>Supplier</th><th>Date</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th>Payment</th></tr></thead>
      <tbody>
        @forelse($purchases as $p)
        <tr>
          <td><a href="{{ route('purchases.show',$p) }}" class="text-blue-600 hover:underline font-medium">{{ $p->reference }}</a></td>
          <td>{{ $p->supplier->name }}</td>
          <td>{{ $p->purchase_date->format('d M Y') }}</td>
          <td class="font-bold">PKR {{ number_format($p->total_amount,2) }}</td>
          <td class="text-emerald-600">PKR {{ number_format($p->paid_amount,2) }}</td>
          <td class="text-red-600">PKR {{ number_format($p->due_amount,2) }}</td>
          <td>@php $sm=['pending'=>'badge-warning','received'=>'badge-success']; @endphp <span class="badge {{ $sm[$p->status]??'badge-gray' }}">{{ ucfirst($p->status) }}</span></td>
          <td>@php $pm=['unpaid'=>'badge-danger','partial'=>'badge-warning','paid'=>'badge-success']; @endphp <span class="badge {{ $pm[$p->payment_status]??'badge-gray' }}">{{ ucfirst($p->payment_status) }}</span></td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center py-8 text-gray-400">No purchases in this period</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
