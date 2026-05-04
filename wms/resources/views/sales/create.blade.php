@extends('layouts.app')
@section('title','New Sale')
@section('page-title','Create Sales Invoice')

@section('content')
<form method="POST" action="{{ route('sales.store') }}" id="saleForm">
@csrf
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-4">
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Sale Details</h3></div>
      <div class="card-body grid grid-cols-2 md:grid-cols-3 gap-4">
        <div>
          <label class="form-label">Customer *</label>
          <select name="customer_id" class="form-select" required>
            <option value="">Select Customer</option>
            @foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
          </select>
        </div>
        <div>
          <label class="form-label">Warehouse *</label>
          <select name="warehouse_id" id="warehouseSelect" class="form-select" required>
            <option value="">Select Warehouse</option>
            @foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
          </select>
        </div>
        <div>
          <label class="form-label">Sale Date *</label>
          <input type="date" name="sale_date" value="{{ date('Y-m-d') }}" class="form-input" required>
        </div>
        <div>
          <label class="form-label">Payment Method</label>
          <select name="payment_method" class="form-select">
            <option value="cash">Cash</option><option value="bank">Bank Transfer</option><option value="cheque">Cheque</option><option value="credit">Credit</option>
          </select>
        </div>
        <div>
          <label class="form-label">Paid Amount</label>
          <input type="number" name="paid_amount" id="paidInput" value="0" class="form-input" min="0" step="0.01">
        </div>
        <div>
          <label class="form-label">Notes</label>
          <input type="text" name="notes" class="form-input" placeholder="Optional">
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3 class="font-semibold text-gray-700">Items</h3>
        <div class="relative">
          <input type="text" id="itemSearch" placeholder="Search & add item..." class="form-input w-56 text-sm">
          <div id="itemDropdown" class="absolute top-full left-0 w-72 bg-white border border-gray-200 rounded-lg shadow-xl z-50 hidden max-h-56 overflow-y-auto"></div>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="table">
          <thead><tr><th>Item</th><th>Unit</th><th>Stock</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th><th></th></tr></thead>
          <tbody id="itemsContainer"></tbody>
        </table>
        <div id="emptyItems" class="text-center py-6 text-gray-400 text-sm"><i class="fas fa-search mb-2 block text-2xl"></i>Search and add items above</div>
      </div>
    </div>
  </div>

  <div class="space-y-4">
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Summary</h3></div>
      <div class="card-body space-y-3 text-sm">
        <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="font-medium">PKR <span id="subtotalDisplay">0.00</span></span></div>
        <div class="flex justify-between items-center">
          <span class="text-gray-500">Discount</span>
          <input type="number" name="discount_amount" id="discountInput" value="0" class="form-input w-28 text-right" min="0" step="0.01">
        </div>
        <div class="flex justify-between items-center">
          <span class="text-gray-500">Tax</span>
          <input type="number" name="tax_amount" id="taxInput" value="0" class="form-input w-28 text-right" min="0" step="0.01">
        </div>
        <div class="flex justify-between items-center">
          <span class="text-gray-500">Shipping</span>
          <input type="number" name="shipping_cost" id="shippingInput" value="0" class="form-input w-28 text-right" min="0" step="0.01">
        </div>
        <div class="border-t pt-3 flex justify-between"><span class="font-bold">Grand Total</span><span class="font-bold text-lg text-blue-600">PKR <span id="grandTotalDisplay">0.00</span></span></div>
      </div>
    </div>
    <button type="submit" class="btn-success w-full justify-center py-3"><i class="fas fa-save"></i> Create Invoice</button>
    <a href="{{ route('sales.index') }}" class="btn-outline w-full justify-center">Cancel</a>
  </div>
</div>
</form>

@push('scripts')
<script>
const items = @json($items);
const itemMap = {};
items.forEach(i => { itemMap[i.id] = i; });

$('#itemSearch').on('input', function(){
  const q = $(this).val().toLowerCase();
  if(q.length < 2){ $('#itemDropdown').addClass('hidden'); return; }
  const wId = parseInt($('#warehouseSelect').val());
  const filtered = items.filter(i => i.name.toLowerCase().includes(q) || (i.sku||'').toLowerCase().includes(q));
  if(!filtered.length){ $('#itemDropdown').addClass('hidden'); return; }
  let html = filtered.slice(0,10).map(i => {
    const stock = i.inventory ? (i.inventory.find(inv => inv.warehouse_id == wId)?.quantity ?? 0) : 0;
    return `<div class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-50 last:border-0" data-id="${i.id}" data-stock="${stock}">
      <p class="text-sm font-medium text-gray-800">${i.name}</p>
      <p class="text-xs text-gray-400">${i.sku} | Stock: <span class="${stock <= 0 ? 'text-red-500' : 'text-emerald-500'}">${parseFloat(stock).toFixed(2)}</span> | PKR ${parseFloat(i.selling_price).toFixed(2)}</p>
    </div>`;
  }).join('');
  $('#itemDropdown').html(html).removeClass('hidden');
});

$(document).on('click', '#itemDropdown [data-id]', function(){
  const id = $(this).data('id');
  const item = itemMap[id];
  const stock = $(this).data('stock');
  if(!item) return;
  WMS.addItemRow('itemsContainer', { id: item.id, name: item.name, sku: item.sku, unit: item.unit?.symbol, stock, selling_price: item.selling_price });
  $('#emptyItems').hide();
  $('#itemSearch').val('');
  $('#itemDropdown').addClass('hidden');
});

$(document).on('click', function(e){ if(!$(e.target).closest('#itemSearch, #itemDropdown').length) $('#itemDropdown').addClass('hidden'); });

$('#discountInput,#taxInput,#shippingInput').on('input', WMS.recalcTotal);
</script>
@endpush
@endsection
