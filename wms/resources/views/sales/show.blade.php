@extends('layouts.app')
@section('title','Sale '.$sale->reference)
@section('page-title','Sale Invoice — '.$sale->reference)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-4">
    <div class="card">
      <div class="card-header">
        <div><h3 class="font-semibold">{{ $sale->reference }}</h3><p class="text-xs text-gray-400">{{ $sale->sale_date->format('d M Y') }}</p></div>
        <div class="flex gap-2">
          @php $sm=['pending'=>'badge-warning','confirmed'=>'badge-success','cancelled'=>'badge-danger']; @endphp
          <span class="badge {{ $sm[$sale->status]??'badge-gray' }}">{{ ucfirst($sale->status) }}</span>
          @php $pm=['unpaid'=>'badge-danger','partial'=>'badge-warning','paid'=>'badge-success']; @endphp
          <span class="badge {{ $pm[$sale->payment_status]??'badge-gray' }}">{{ ucfirst($sale->payment_status) }}</span>
        </div>
      </div>
      <div class="card-body grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
        <div><p class="text-gray-500">Customer</p><p class="font-medium">{{ $sale->customer->name }}</p><p class="text-xs text-gray-400">{{ $sale->customer->phone }}</p></div>
        <div><p class="text-gray-500">Warehouse</p><p class="font-medium">{{ $sale->warehouse->name }}</p></div>
        <div><p class="text-gray-500">Created By</p><p class="font-medium">{{ $sale->creator->name }}</p></div>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Sale Items</h3></div>
      <div class="overflow-x-auto">
        <table class="table">
          <thead><tr><th>#</th><th>Item</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th><th>Profit</th></tr></thead>
          <tbody>
            @foreach($sale->items as $i => $item)
            <tr>
              <td>{{ $i+1 }}</td>
              <td><p class="font-medium">{{ $item->item->name }}</p><p class="text-xs text-gray-400">{{ $item->item->sku }}</p></td>
              <td>{{ number_format($item->quantity,2) }}</td>
              <td>PKR {{ number_format($item->unit_price,2) }}</td>
              <td class="font-semibold">PKR {{ number_format($item->subtotal,2) }}</td>
              <td class="text-{{ $item->profit >= 0 ? 'emerald' : 'red' }}-600 font-semibold">PKR {{ number_format($item->profit,2) }}</td>
            </tr>
            @endforeach
          </tbody>
          <tfoot class="bg-gray-50">
            <tr><td colspan="4" class="text-right font-semibold px-4 py-2">Subtotal:</td><td colspan="2" class="px-4 py-2 font-bold">PKR {{ number_format($sale->subtotal,2) }}</td></tr>
            @if($sale->discount_amount > 0)<tr><td colspan="4" class="text-right px-4 py-2 text-gray-500">Discount:</td><td colspan="2" class="px-4 py-2 text-red-500">- PKR {{ number_format($sale->discount_amount,2) }}</td></tr>@endif
            @if($sale->tax_amount > 0)<tr><td colspan="4" class="text-right px-4 py-2 text-gray-500">Tax:</td><td colspan="2" class="px-4 py-2">PKR {{ number_format($sale->tax_amount,2) }}</td></tr>@endif
            <tr><td colspan="4" class="text-right font-bold px-4 py-2 text-blue-600">Grand Total:</td><td colspan="2" class="px-4 py-2 font-bold text-blue-600 text-lg">PKR {{ number_format($sale->total_amount,2) }}</td></tr>
          </tfoot>
        </table>
      </div>
    </div>

    @if($sale->payments->count())
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Payments</h3></div>
      <div class="overflow-x-auto">
        <table class="table">
          <thead><tr><th>Date</th><th>Amount</th><th>Method</th><th>Reference</th></tr></thead>
          <tbody>
            @foreach($sale->payments as $pay)
            <tr>
              <td>{{ $pay->payment_date->format('d M Y') }}</td>
              <td class="font-semibold text-emerald-600">PKR {{ number_format($pay->amount,2) }}</td>
              <td class="capitalize">{{ $pay->payment_method }}</td>
              <td class="text-gray-400 text-xs">{{ $pay->reference ?: '—' }}</td>
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
        <div class="flex justify-between"><span class="text-gray-500">Total</span><span class="font-bold text-lg">PKR {{ number_format($sale->total_amount,2) }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Paid</span><span class="font-semibold text-emerald-600">PKR {{ number_format($sale->paid_amount,2) }}</span></div>
        <div class="flex justify-between border-t pt-3"><span class="font-semibold">Due</span><span class="font-bold text-red-600 text-lg">PKR {{ number_format($sale->due_amount,2) }}</span></div>
      </div>
    </div>

    <a href="{{ route('sales.invoice',$sale) }}" target="_blank" class="btn btn-sm w-full justify-center border border-violet-300 text-violet-700 hover:bg-violet-50"><i class="fas fa-print"></i> Print Invoice</a>
    <a href="{{ route('reports.sale-invoice-pdf',$sale) }}" target="_blank" class="btn-danger btn-sm w-full justify-center"><i class="fas fa-file-pdf"></i> Download PDF Invoice</a>

    @if($sale->due_amount > 0)
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-sm">Add Payment</h3></div>
      <div class="card-body">
        <form method="POST" action="{{ route('sales.payment',$sale) }}" class="space-y-3">
          @csrf
          <div><label class="form-label text-xs">Amount</label><input type="number" name="amount" class="form-input" max="{{ $sale->due_amount }}" step="0.01" min="0.01" required></div>
          <div><label class="form-label text-xs">Method</label>
            <select name="payment_method" class="form-select"><option value="cash">Cash</option><option value="bank">Bank</option><option value="cheque">Cheque</option></select>
          </div>
          <div><label class="form-label text-xs">Date</label><input type="date" name="payment_date" value="{{ date('Y-m-d') }}" class="form-input" required></div>
          <div><label class="form-label text-xs">Reference</label><input type="text" name="reference" class="form-input" placeholder="Optional"></div>
          <button type="submit" class="btn-success w-full justify-center btn-sm"><i class="fas fa-credit-card"></i> Record Payment</button>
        </form>
      </div>
    </div>
    @endif

    <a href="{{ route('sales.index') }}" class="btn-outline w-full justify-center"><i class="fas fa-arrow-left"></i> Back</a>
  </div>
</div>
@endsection
