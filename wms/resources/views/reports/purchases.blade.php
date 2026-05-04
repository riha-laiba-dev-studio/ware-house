@extends('layouts.app')
@section('title','Purchase Report')
@section('page-title','Purchase Report')

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
    <div><label class="form-label text-xs">Supplier</label>
      <select name="supplier_id" class="form-select w-36">
        <option value="">All</option>
        @foreach($suppliers as $s)<option value="{{ $s->id }}" {{ request('supplier_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach
      </select>
    </div>
    <div class="flex items-end gap-2">
      <button class="btn-primary btn-sm"><i class="fas fa-filter"></i> Filter</button>
      <a href="{{ route('reports.purchases') }}" class="btn-outline btn-sm">Reset</a>
    </div>
  </form>
  <div class="flex items-center gap-2">
    <a href="{{ route('reports.purchases.csv') }}?{{ request()->getQueryString() }}" class="btn-outline btn-sm">
      <i class="fas fa-file-csv text-emerald-600"></i> CSV
    </a>
    <a href="{{ route('reports.purchases.pdf') }}?{{ request()->getQueryString() }}" class="btn-outline btn-sm" target="_blank">
      <i class="fas fa-file-pdf text-red-500"></i> PDF
    </a>
    <button onclick="window.print()" class="btn-outline btn-sm"><i class="fas fa-print text-blue-500"></i> Print</button>
  </div>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
  <div class="card p-4 flex items-center gap-3">
    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center"><i class="fas fa-cart-flatbed"></i></div>
    <div><p class="text-xs text-gray-500">Orders</p><p class="text-xl font-bold text-gray-800">{{ $purchases->count() }}</p></div>
  </div>
  <div class="card p-4 flex items-center gap-3">
    <div class="w-10 h-10 bg-gray-100 text-gray-600 rounded-xl flex items-center justify-center"><i class="fas fa-dollar-sign"></i></div>
    <div><p class="text-xs text-gray-500">Total Amount</p><p class="text-xl font-bold text-gray-800">PKR {{ number_format($total,0) }}</p></div>
  </div>
  <div class="card p-4 flex items-center gap-3">
    <div class="w-10 h-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center"><i class="fas fa-check-circle"></i></div>
    <div><p class="text-xs text-gray-500">Paid</p><p class="text-xl font-bold text-emerald-700">PKR {{ number_format($totalPaid,0) }}</p></div>
  </div>
  <div class="card p-4 flex items-center gap-3">
    <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center"><i class="fas fa-clock"></i></div>
    <div><p class="text-xs text-gray-500">Due</p><p class="text-xl font-bold text-red-600">PKR {{ number_format($totalDue,0) }}</p></div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h3 class="font-semibold text-gray-700">Purchase Orders</h3>
    <span class="badge badge-info">{{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</span>
  </div>
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>#</th><th>Reference</th><th>Supplier</th><th>Date</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th>Payment</th><th></th></tr></thead>
      <tbody>
        @forelse($purchases as $i => $p)
        <tr>
          <td class="text-gray-400 text-xs">{{ $i+1 }}</td>
          <td><a href="{{ route('purchases.show',$p) }}" class="text-blue-600 hover:underline font-mono text-xs font-semibold">{{ $p->reference }}</a></td>
          <td class="font-medium">{{ $p->supplier->name }}</td>
          <td class="text-gray-500">{{ $p->purchase_date->format('d M Y') }}</td>
          <td class="font-bold">PKR {{ number_format($p->total_amount,2) }}</td>
          <td class="text-emerald-600 font-semibold">PKR {{ number_format($p->paid_amount,2) }}</td>
          <td class="text-red-600 font-semibold">PKR {{ number_format($p->due_amount,2) }}</td>
          <td>@php $sm=['pending'=>'badge-warning','received'=>'badge-success']; @endphp<span class="badge {{ $sm[$p->status]??'badge-gray' }}">{{ ucfirst($p->status) }}</span></td>
          <td>@php $pm=['unpaid'=>'badge-danger','partial'=>'badge-warning','paid'=>'badge-success']; @endphp<span class="badge {{ $pm[$p->payment_status]??'badge-gray' }}">{{ ucfirst($p->payment_status) }}</span></td>
          <td class="flex items-center gap-2">
            <a href="{{ route('purchases.show',$p) }}" class="text-blue-500 hover:text-blue-700" title="View"><i class="fas fa-eye text-sm"></i></a>
            <a href="{{ route('reports.purchase-order-pdf', $p) }}" class="text-red-500 hover:text-red-700" title="PDF" target="_blank"><i class="fas fa-file-pdf text-sm"></i></a>
          </td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center py-10 text-gray-400"><i class="fas fa-cart-flatbed text-3xl mb-2 block opacity-30"></i>No purchases in this period</td></tr>
        @endforelse
      </tbody>
      @if($purchases->count())
      <tfoot class="bg-gray-50">
        <tr>
          <td colspan="4" class="px-4 py-3 font-semibold text-gray-600 text-right">Totals:</td>
          <td class="px-4 py-3 font-bold">PKR {{ number_format($total,2) }}</td>
          <td class="px-4 py-3 font-bold text-emerald-600">PKR {{ number_format($totalPaid,2) }}</td>
          <td class="px-4 py-3 font-bold text-red-600">PKR {{ number_format($totalDue,2) }}</td>
          <td colspan="3"></td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>
@endsection
