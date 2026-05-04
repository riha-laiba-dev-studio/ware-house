@extends('layouts.app')
@section('title','Alerts & Notifications')
@section('page-title','Alerts & Notifications')

@section('content')
{{-- Summary Cards --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
  <div class="stat-card border-l-4 border-red-500">
    <div class="stat-icon bg-red-100 text-red-600"><i class="fas fa-triangle-exclamation"></i></div>
    <div>
      <p class="text-xs text-gray-500 font-medium">Low Stock Items</p>
      <p class="text-2xl font-bold {{ $totalLowStock > 0 ? 'text-red-600' : 'text-gray-800' }}">{{ $totalLowStock }}</p>
      <p class="text-xs text-gray-400">Items below alert level</p>
    </div>
  </div>
  <div class="stat-card border-l-4 border-amber-500">
    <div class="stat-icon bg-amber-100 text-amber-600"><i class="fas fa-money-bill-wave"></i></div>
    <div>
      <p class="text-xs text-gray-500 font-medium">Sales Due</p>
      <p class="text-2xl font-bold text-amber-600">PKR {{ number_format($totalSalesDue,0) }}</p>
      <p class="text-xs text-gray-400">{{ $overduePayablesSales->count() }} customers</p>
    </div>
  </div>
  <div class="stat-card border-l-4 border-orange-500">
    <div class="stat-icon bg-orange-100 text-orange-600"><i class="fas fa-truck"></i></div>
    <div>
      <p class="text-xs text-gray-500 font-medium">Purchase Due</p>
      <p class="text-2xl font-bold text-orange-600">PKR {{ number_format($totalPurchaseDue,0) }}</p>
      <p class="text-xs text-gray-400">{{ $overduePayablesPurchases->count() }} suppliers</p>
    </div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

  {{-- Low Stock Alerts --}}
  <div class="card">
    <div class="card-header">
      <div class="flex items-center gap-2">
        <i class="fas fa-triangle-exclamation text-red-500"></i>
        <h3 class="font-semibold text-gray-700">Low Stock Alerts ({{ $totalLowStock }})</h3>
      </div>
      @if($totalLowStock > 0)
      <button onclick="document.getElementById('lowStockEmailModal').classList.remove('hidden')" class="btn-danger btn-sm">
        <i class="fas fa-envelope"></i> Email Alert
      </button>
      @endif
    </div>
    <div class="overflow-y-auto max-h-80">
      @forelse($lowStockItems as $s)
      <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 hover:bg-red-50 transition-colors">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg {{ $s->quantity <= 0 ? 'bg-red-100' : 'bg-amber-100' }} flex items-center justify-center flex-shrink-0">
            <i class="fas fa-box {{ $s->quantity <= 0 ? 'text-red-600' : 'text-amber-600' }} text-xs"></i>
          </div>
          <div>
            <p class="text-sm font-semibold text-gray-800">{{ $s->item->name }}</p>
            <p class="text-xs text-gray-400">{{ $s->warehouse->name }} • {{ $s->item->category->name }}</p>
          </div>
        </div>
        <div class="text-right">
          <p class="text-sm font-bold {{ $s->quantity <= 0 ? 'text-red-600' : 'text-amber-600' }}">
            {{ number_format($s->quantity,2) }} / {{ $s->item->alert_quantity }} {{ $s->item->unit->symbol }}
          </p>
          <span class="text-xs {{ $s->quantity <= 0 ? 'text-red-500' : 'text-amber-500' }}">
            {{ $s->quantity <= 0 ? 'Out of Stock' : 'Low Stock' }}
          </span>
        </div>
      </div>
      @empty
      <div class="py-12 text-center text-gray-400">
        <i class="fas fa-check-circle text-3xl text-emerald-400 mb-3 block"></i>
        <p class="font-medium text-emerald-600">All items are well stocked!</p>
      </div>
      @endforelse
    </div>
    @if($totalLowStock > 0)
    <div class="px-5 py-3 bg-gray-50 border-t border-gray-100">
      <a href="{{ route('reports.stock') }}" class="text-xs text-blue-600 hover:underline"><i class="fas fa-arrow-right mr-1"></i> View Full Stock Report</a>
    </div>
    @endif
  </div>

  {{-- Sales Due --}}
  <div class="card">
    <div class="card-header">
      <div class="flex items-center gap-2">
        <i class="fas fa-receipt text-amber-500"></i>
        <h3 class="font-semibold text-gray-700">Customers with Due Payments ({{ $overduePayablesSales->count() }})</h3>
      </div>
      @if($overduePayablesSales->count())
      <button onclick="document.getElementById('salesDueEmailModal').classList.remove('hidden')" class="btn-warning btn-sm">
        <i class="fas fa-envelope"></i> Email
      </button>
      @endif
    </div>
    <div class="overflow-y-auto max-h-80">
      @forelse($overduePayablesSales as $sale)
      <div class="flex items-center justify-between px-5 py-3 border-b border-gray-50 hover:bg-amber-50 transition-colors">
        <div>
          <p class="text-sm font-semibold text-gray-800">{{ $sale->customer->name }}</p>
          <p class="text-xs text-gray-400">
            <a href="{{ route('sales.show',$sale) }}" class="text-blue-600 hover:underline">{{ $sale->reference }}</a>
            • {{ $sale->sale_date->format('d M Y') }}
          </p>
        </div>
        <div class="text-right">
          <p class="text-sm font-bold text-red-600">PKR {{ number_format($sale->due_amount,2) }}</p>
          <span class="badge {{ $sale->payment_status==='unpaid'?'badge-danger':'badge-warning' }} text-xs">{{ ucfirst($sale->payment_status) }}</span>
        </div>
      </div>
      @empty
      <div class="py-12 text-center text-gray-400">
        <i class="fas fa-check-circle text-3xl text-emerald-400 mb-3 block"></i>
        <p class="font-medium text-emerald-600">No outstanding sales dues!</p>
      </div>
      @endforelse
    </div>
    @if($overduePayablesSales->count())
    <div class="px-5 py-3 bg-amber-50 border-t border-amber-100 flex justify-between items-center">
      <span class="text-xs text-amber-700 font-medium">Total: PKR {{ number_format($totalSalesDue,2) }}</span>
      <a href="{{ route('reports.sales') }}" class="text-xs text-blue-600 hover:underline">View Report →</a>
    </div>
    @endif
  </div>

  {{-- Purchase Due --}}
  <div class="card lg:col-span-2">
    <div class="card-header">
      <div class="flex items-center gap-2">
        <i class="fas fa-truck text-orange-500"></i>
        <h3 class="font-semibold text-gray-700">Suppliers with Due Payments ({{ $overduePayablesPurchases->count() }})</h3>
      </div>
      @if($overduePayablesPurchases->count())
      <button onclick="document.getElementById('purchaseDueEmailModal').classList.remove('hidden')" class="btn-sm bg-orange-500 text-white hover:bg-orange-600 rounded-lg px-3 py-1.5 text-xs font-medium inline-flex items-center gap-1.5">
        <i class="fas fa-envelope"></i> Email Alert
      </button>
      @endif
    </div>
    <div class="overflow-x-auto">
      <table class="table">
        <thead>
          <tr><th>Reference</th><th>Supplier</th><th>PO Date</th><th>Total</th><th>Paid</th><th>Due</th><th>Status</th><th>Action</th></tr>
        </thead>
        <tbody>
          @forelse($overduePayablesPurchases as $p)
          <tr>
            <td><a href="{{ route('purchases.show',$p) }}" class="text-blue-600 hover:underline font-mono text-xs">{{ $p->reference }}</a></td>
            <td class="font-medium">{{ $p->supplier->name }}</td>
            <td>{{ $p->purchase_date->format('d M Y') }}</td>
            <td>PKR {{ number_format($p->total_amount,2) }}</td>
            <td class="text-emerald-600">PKR {{ number_format($p->paid_amount,2) }}</td>
            <td class="font-bold text-red-600">PKR {{ number_format($p->due_amount,2) }}</td>
            <td><span class="badge {{ $p->payment_status==='unpaid'?'badge-danger':'badge-warning' }}">{{ ucfirst($p->payment_status) }}</span></td>
            <td><a href="{{ route('purchases.show',$p) }}" class="btn-outline btn-sm">Pay</a></td>
          </tr>
          @empty
          <tr><td colspan="8" class="text-center py-8 text-gray-400"><i class="fas fa-check-circle text-emerald-400 mr-2"></i>No outstanding purchase dues!</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    @if($overduePayablesPurchases->count())
    <div class="px-5 py-3 bg-orange-50 border-t border-orange-100 flex justify-between items-center">
      <span class="text-xs text-orange-700 font-medium">Total Due: PKR {{ number_format($totalPurchaseDue,2) }}</span>
      <a href="{{ route('reports.purchases') }}" class="text-xs text-blue-600 hover:underline">View Report →</a>
    </div>
    @endif
  </div>
</div>

{{-- Low Stock Email Modal --}}
<div id="lowStockEmailModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
      <h3 class="font-semibold text-gray-800"><i class="fas fa-envelope text-red-500 mr-2"></i>Send Low Stock Alert Email</h3>
      <button onclick="document.getElementById('lowStockEmailModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" action="{{ route('notifications.send-low-stock') }}" class="p-6 space-y-4">
      @csrf
      <p class="text-sm text-gray-600">Send a detailed low stock alert email to the specified recipient. <strong>{{ $totalLowStock }}</strong> items will be included.</p>
      <div>
        <label class="form-label">Recipient Email *</label>
        <input type="email" name="email" class="form-input" placeholder="manager@example.com" required value="{{ auth()->user()->email }}">
      </div>
      <div class="flex gap-3 justify-end pt-2">
        <button type="button" onclick="document.getElementById('lowStockEmailModal').classList.add('hidden')" class="btn-outline">Cancel</button>
        <button type="submit" class="btn-danger"><i class="fas fa-paper-plane"></i> Send Alert</button>
      </div>
    </form>
  </div>
</div>

{{-- Sales Due Email Modal --}}
<div id="salesDueEmailModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
      <h3 class="font-semibold text-gray-800"><i class="fas fa-envelope text-amber-500 mr-2"></i>Send Sales Due Alert</h3>
      <button onclick="document.getElementById('salesDueEmailModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" action="{{ route('notifications.send-payment-due') }}" class="p-6 space-y-4">
      @csrf
      <input type="hidden" name="type" value="sales">
      <p class="text-sm text-gray-600">Send payment due reminder for <strong>{{ $overduePayablesSales->count() }}</strong> outstanding sales invoices.</p>
      <div>
        <label class="form-label">Recipient Email *</label>
        <input type="email" name="email" class="form-input" placeholder="accounts@example.com" required value="{{ auth()->user()->email }}">
      </div>
      <div class="flex gap-3 justify-end pt-2">
        <button type="button" onclick="document.getElementById('salesDueEmailModal').classList.add('hidden')" class="btn-outline">Cancel</button>
        <button type="submit" class="btn-warning"><i class="fas fa-paper-plane"></i> Send Alert</button>
      </div>
    </form>
  </div>
</div>

{{-- Purchase Due Email Modal --}}
<div id="purchaseDueEmailModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
      <h3 class="font-semibold text-gray-800"><i class="fas fa-envelope text-orange-500 mr-2"></i>Send Purchase Due Alert</h3>
      <button onclick="document.getElementById('purchaseDueEmailModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
    </div>
    <form method="POST" action="{{ route('notifications.send-payment-due') }}" class="p-6 space-y-4">
      @csrf
      <input type="hidden" name="type" value="purchases">
      <p class="text-sm text-gray-600">Send payment due reminder for <strong>{{ $overduePayablesPurchases->count() }}</strong> outstanding purchase orders.</p>
      <div>
        <label class="form-label">Recipient Email *</label>
        <input type="email" name="email" class="form-input" placeholder="accounts@example.com" required value="{{ auth()->user()->email }}">
      </div>
      <div class="flex gap-3 justify-end pt-2">
        <button type="button" onclick="document.getElementById('purchaseDueEmailModal').classList.add('hidden')" class="btn-outline">Cancel</button>
        <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white btn btn-sm"><i class="fas fa-paper-plane"></i> Send Alert</button>
      </div>
    </form>
  </div>
</div>
@endsection
