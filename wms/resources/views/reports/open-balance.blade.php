@extends('layouts.app')
@section('title','Open Balance Sheet')
@section('page-title','Open Balance Sheet')

@section('content')
<div class="flex justify-end mb-4 gap-2">
  <a href="{{ route('reports.open-balance.pdf') }}" class="btn-outline btn-sm" target="_blank">
    <i class="fas fa-file-pdf text-red-500"></i> Export PDF
  </a>
  <button onclick="window.print()" class="btn-outline btn-sm"><i class="fas fa-print text-blue-500"></i> Print</button>
</div>
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <div class="card">
    <div class="card-header"><h3 class="font-semibold text-gray-700">Supplier Payables</h3><span class="badge badge-danger">To Pay</span></div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>Supplier</th><th>Code</th><th>Total</th><th>Paid</th><th>Due</th></tr></thead>
        <tbody>
          @forelse($suppliers as $s)
          <tr>
            <td class="font-medium">{{ $s->name }}</td>
            <td class="text-xs font-mono text-blue-600">{{ $s->code }}</td>
            <td>PKR {{ number_format($s->purchases_sum_total_amount ?? 0,2) }}</td>
            <td class="text-emerald-600">PKR {{ number_format($s->purchases_sum_paid_amount ?? 0,2) }}</td>
            <td class="text-red-600 font-bold">PKR {{ number_format($s->purchases_sum_due_amount ?? 0,2) }}</td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center py-6 text-gray-400">No supplier records</td></tr>
          @endforelse
        </tbody>
        <tfoot class="bg-gray-50 font-semibold">
          <tr>
            <td colspan="4" class="px-4 py-2 text-right">Total Due to Suppliers:</td>
            <td class="px-4 py-2 text-red-700 text-lg font-bold">PKR {{ number_format($suppliers->sum('purchases_sum_due_amount'),2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h3 class="font-semibold text-gray-700">Customer Receivables</h3><span class="badge badge-success">To Receive</span></div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead><tr><th>Customer</th><th>Code</th><th>Total</th><th>Paid</th><th>Due</th></tr></thead>
        <tbody>
          @forelse($customers as $c)
          <tr>
            <td class="font-medium">{{ $c->name }}</td>
            <td class="text-xs font-mono text-blue-600">{{ $c->code }}</td>
            <td>PKR {{ number_format($c->sales_sum_total_amount ?? 0,2) }}</td>
            <td class="text-emerald-600">PKR {{ number_format($c->sales_sum_paid_amount ?? 0,2) }}</td>
            <td class="text-red-600 font-bold">PKR {{ number_format($c->sales_sum_due_amount ?? 0,2) }}</td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center py-6 text-gray-400">No customer records</td></tr>
          @endforelse
        </tbody>
        <tfoot class="bg-gray-50 font-semibold">
          <tr>
            <td colspan="4" class="px-4 py-2 text-right">Total Due from Customers:</td>
            <td class="px-4 py-2 text-emerald-700 text-lg font-bold">PKR {{ number_format($customers->sum('sales_sum_due_amount'),2) }}</td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
