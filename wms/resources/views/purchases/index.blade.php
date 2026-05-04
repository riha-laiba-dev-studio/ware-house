@extends('layouts.app')
@section('title','Purchases')
@section('page-title','Purchase Orders')

@section('content')
<div class="flex items-center justify-between mb-5">
  <form class="flex gap-2 flex-wrap">
    <select name="supplier_id" class="form-select w-40">
      <option value="">All Suppliers</option>
      @foreach($suppliers as $s)<option value="{{ $s->id }}" {{ request('supplier_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>@endforeach
    </select>
    <select name="status" class="form-select w-36">
      <option value="">All Status</option>
      <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
      <option value="received" {{ request('status')=='received'?'selected':'' }}>Received</option>
    </select>
    <input type="date" name="from" value="{{ request('from') }}" class="form-input w-36">
    <input type="date" name="to" value="{{ request('to') }}" class="form-input w-36">
    <button class="btn-primary btn-sm">Filter</button>
    <a href="{{ route('purchases.index') }}" class="btn-outline btn-sm">Reset</a>
  </form>
  <a href="{{ route('purchases.create') }}" class="btn-primary"><i class="fas fa-plus"></i> New Purchase</a>
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
  <div class="card p-4"><p class="text-xs text-gray-500">Total Amount</p><p class="text-lg font-bold text-gray-800">PKR {{ number_format($purchases->sum('total_amount'),0) }}</p></div>
  <div class="card p-4"><p class="text-xs text-gray-500">Total Paid</p><p class="text-lg font-bold text-emerald-600">PKR {{ number_format($purchases->sum('paid_amount'),0) }}</p></div>
  <div class="card p-4"><p class="text-xs text-gray-500">Total Due</p><p class="text-lg font-bold text-red-600">PKR {{ number_format($purchases->sum('due_amount'),0) }}</p></div>
  <div class="card p-4"><p class="text-xs text-gray-500">Total Orders</p><p class="text-lg font-bold text-gray-800">{{ $purchases->total() }}</p></div>
</div>

<div class="card">
  <div class="overflow-x-auto">
    <table class="table">
      <thead><tr><th>Reference</th><th>Supplier</th><th>Warehouse</th><th>Date</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th>Payment</th><th>Action</th></tr></thead>
      <tbody>
        @forelse($purchases as $p)
        <tr>
          <td><a href="{{ route('purchases.show',$p) }}" class="text-blue-600 font-medium hover:underline">{{ $p->reference }}</a></td>
          <td>{{ $p->supplier->name }}</td>
          <td>{{ $p->warehouse->name }}</td>
          <td class="text-gray-500">{{ $p->purchase_date->format('d M Y') }}</td>
          <td class="font-semibold">PKR {{ number_format($p->total_amount,2) }}</td>
          <td class="text-emerald-600">PKR {{ number_format($p->paid_amount,2) }}</td>
          <td class="text-red-600 font-semibold">PKR {{ number_format($p->due_amount,2) }}</td>
          <td>
            @php $statusMap=['pending'=>'badge-warning','received'=>'badge-success','partial'=>'badge-info','cancelled'=>'badge-danger']; @endphp
            <span class="badge {{ $statusMap[$p->status] ?? 'badge-gray' }}">{{ ucfirst($p->status) }}</span>
          </td>
          <td>
            @php $payMap=['unpaid'=>'badge-danger','partial'=>'badge-warning','paid'=>'badge-success']; @endphp
            <span class="badge {{ $payMap[$p->payment_status] ?? 'badge-gray' }}">{{ ucfirst($p->payment_status) }}</span>
          </td>
          <td><a href="{{ route('purchases.show',$p) }}" class="btn-outline btn-sm"><i class="fas fa-eye"></i></a></td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center py-8 text-gray-400">No purchases found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-4 py-3 border-t border-gray-100">{{ $purchases->links() }}</div>
</div>
@endsection
