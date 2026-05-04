@extends('layouts.app')
@section('title','Purchase '.$purchase->reference)
@section('page-title','Purchase Order — '.$purchase->reference)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-4">
    <div class="card">
      <div class="card-header">
        <div>
          <h3 class="font-semibold text-gray-800">{{ $purchase->reference }}</h3>
          <p class="text-xs text-gray-400 mt-0.5">{{ $purchase->purchase_date->format('d M Y') }}</p>
        </div>
        <div class="flex items-center gap-2">
          @php $statusMap=['pending'=>'badge-warning','received'=>'badge-success','cancelled'=>'badge-danger']; @endphp
          <span class="badge {{ $statusMap[$purchase->status] ?? 'badge-gray' }}">{{ ucfirst($purchase->status) }}</span>
          @php $payMap=['unpaid'=>'badge-danger','partial'=>'badge-warning','paid'=>'badge-success']; @endphp
          <span class="badge {{ $payMap[$purchase->payment_status] ?? 'badge-gray' }}">{{ ucfirst($purchase->payment_status) }}</span>
        </div>
      </div>
      <div class="card-body grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
        <div><p class="text-gray-500">Supplier</p><p class="font-medium">{{ $purchase->supplier->name }}</p><p class="text-xs text-gray-400">{{ $purchase->supplier->phone }}</p></div>
        <div><p class="text-gray-500">Warehouse</p><p class="font-medium">{{ $purchase->warehouse->name }}</p></div>
        <div><p class="text-gray-500">Created By</p><p class="font-medium">{{ $purchase->creator->name }}</p></div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Order Items</h3></div>
      <div class="overflow-x-auto">
        <table class="table">
          <thead><tr><th>#</th><th>Item</th><th>Qty</th><th>Received</th><th>Unit Cost</th><th>Subtotal</th></tr></thead>
          <tbody>
            @foreach($purchase->items as $i => $item)
            <tr>
              <td>{{ $i+1 }}</td>
              <td><p class="font-medium">{{ $item->item->name }}</p><p class="text-xs text-gray-400">{{ $item->item->sku }}</p></td>
              <td>{{ number_format($item->quantity,2) }}</td>
              <td><span class="{{ $item->received_quantity >= $item->quantity ? 'badge badge-success' : 'badge badge-warning' }}">{{ number_format($item->received_quantity,2) }}</span></td>
              <td>PKR {{ number_format($item->unit_cost,2) }}</td>
              <td class="font-semibold">PKR {{ number_format($item->subtotal,2) }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot class="bg-gray-50">
            <tr><td colspan="5" class="text-right font-semibold px-4 py-2">Subtotal:</td><td class="px-4 py-2 font-bold">PKR {{ number_format($purchase->subtotal,2) }}</td></tr>
            @if($purchase->shipping_cost > 0)<tr><td colspan="5" class="text-right px-4 py-2 text-gray-500">Shipping:</td><td class="px-4 py-2">PKR {{ number_format($purchase->shipping_cost,2) }}</td></tr>@endif
            <tr><td colspan="5" class="text-right font-bold px-4 py-2 text-blue-600">Grand Total:</td><td class="px-4 py-2 font-bold text-blue-600 text-lg">PKR {{ number_format($purchase->total_amount,2) }}</td></tr>
          </tfoot>
        </table>
      </div>
    </div>

    @if($purchase->payments->count())
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Payment History</h3></div>
      <div class="overflow-x-auto">
        <table class="table">
          <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Ref</th><th>By</th></tr></thead>
          <tbody>
            @foreach($purchase->payments as $pay)
            <tr>
              <td>{{ $pay->payment_date->format('d M Y') }}</td>
              <td class="font-semibold text-emerald-600">PKR {{ number_format($pay->amount,2) }}</td>
              <td class="capitalize">{{ $pay->payment_method }}</td>
              <td class="text-gray-400 text-xs">{{ $pay->reference ?: '—' }}</td>
              <td>{{ $pay->creator->name }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif
  </div>

  <div class="space-y-4">
    <div class="card">
      <div class="card-body space-y-3 text-sm">
        <div class="flex justify-between"><span class="text-gray-500">Total Amount</span><span class="font-bold text-lg">PKR {{ number_format($purchase->total_amount,2) }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Paid</span><span class="font-semibold text-emerald-600">PKR {{ number_format($purchase->paid_amount,2) }}</span></div>
        <div class="flex justify-between border-t pt-3"><span class="font-semibold">Due Amount</span><span class="font-bold text-red-600 text-lg">PKR {{ number_format($purchase->due_amount,2) }}</span></div>
      </div>
    </div>

    @if($purchase->status === 'pending')
    <form method="POST" action="{{ route('purchases.receive',$purchase) }}">
      @csrf
      <button class="btn-success w-full justify-center"><i class="fas fa-check-circle"></i> Receive Stock</button>
    </form>
    @endif

    @if($purchase->due_amount > 0)
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700 text-sm">Add Payment</h3></div>
      <div class="card-body">
        <form method="POST" action="{{ route('purchases.payment',$purchase) }}" class="space-y-3">
          @csrf
          <div><label class="form-label text-xs">Amount (max PKR {{ number_format($purchase->due_amount,2) }})</label>
            <input type="number" name="amount" class="form-input" max="{{ $purchase->due_amount }}" step="0.01" min="0.01" required>
          </div>
          <div><label class="form-label text-xs">Method</label>
            <select name="payment_method" class="form-select"><option value="cash">Cash</option><option value="bank">Bank Transfer</option><option value="cheque">Cheque</option></select>
          </div>
          <div><label class="form-label text-xs">Date</label><input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-input" required></div>
          <div><label class="form-label text-xs">Reference</label><input type="text" name="reference" class="form-input" placeholder="Optional"></div>
          <button type="submit" class="btn-primary w-full justify-center btn-sm"><i class="fas fa-credit-card"></i> Record Payment</button>
        </form>
      </div>
    </div>
    @endif

    <a href="{{ route('purchases.index') }}" class="btn-outline w-full justify-center"><i class="fas fa-arrow-left"></i> Back to Purchases</a>
  </div>
</div>
@endsection
