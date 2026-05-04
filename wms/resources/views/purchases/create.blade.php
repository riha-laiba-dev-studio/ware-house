@extends('layouts.app')
@section('title','New Purchase')
@section('page-title','Create Purchase Order')

@section('content')
<form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
@csrf
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-4">
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Purchase Details</h3></div>
      <div class="card-body grid grid-cols-2 md:grid-cols-3 gap-4">
        <div>
          <label class="form-label">Supplier *</label>
          <select name="supplier_id" class="form-select" required>
            <option value="">Select Supplier</option>
            @foreach($suppliers as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
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
          <label class="form-label">Purchase Date *</label>
          <input type="date" name="purchase_date" value="{{ date('Y-m-d') }}" class="form-input" required>
        </div>
        <div>
          <label class="form-label">Shipping Cost</label>
          <input type="number" name="shipping_cost" id="shippingInput" value="0" class="form-input" min="0" step="0.01">
        </div>
        <div class="md:col-span-2">
          <label class="form-label">Notes</label>
          <input type="text" name="notes" class="form-input" placeholder="Optional notes">
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
          <thead><tr><th>Item</th><th>Unit</th><th>Available</th><th>Qty</th><th>Unit Cost</th><th>Subtotal</th><th></th></tr></thead>
          <tbody id="itemsContainer"></tbody>
        </table>
        <div id="emptyItems" class="text-center py-6 text-gray-400 text-sm"><i class="fas fa-search mb-2 block text-2xl"></i>Search and add items above</div>
      </div>
    </div>
  </div>

  <div class="space-y-4">
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Order Summary</h3></div>
      <div class="card-body space-y-3 text-sm">
        <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span class="font-medium" id="subtotalDisplay">0.00</span></div>
        <div class="flex justify-between items-center">
          <span class="text-gray-500">Shipping</span>
          <span class="font-medium" id="shippingDisplay">0.00</span>
        </div>
        <div class="border-t border-gray-100 pt-3 flex justify-between">
          <span class="font-semibold text-gray-700">Grand Total</span>
          <span class="font-bold text-lg text-blue-600">PKR <span id="grandTotalDisplay">0.00</span></span>
        </div>
      </div>
    </div>
    <button type="submit" class="btn-primary w-full justify-center py-3"><i class="fas fa-save"></i> Create Purchase Order</button>
    <a href="{{ route('purchases.index') }}" class="btn-outline w-full justify-center">Cancel</a>
  </div>
</div>
</form>

@push('scripts')
<script>
const items = @json($items);
const itemMap = {};
items.forEach(i => itemMap[i.id] = i);

$('#itemSearch').on('input', function(){
  const q = $(this).val().toLowerCase();
  if(q.length < 2){ $('#itemDropdown').addClass('hidden'); return; }
  const filtered = items.filter(i => i.name.toLowerCase().includes(q) || i.sku.toLowerCase().includes(q));
  if(filtered.length === 0){ $('#itemDropdown').addClass('hidden'); return; }
  const wId = $('#warehouseSelect').val();
  let html = filtered.slice(0,10).map(i => {
    const stock = i.inventory ? i.inventory.find(inv => inv.warehouse_id == wId)?.quantity ?? 0 : 0;
    return `<div class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-50 last:border-0" data-id="${i.id}">
      <p class="text-sm font-medium text-gray-800">${i.name}</p>
      <p class="text-xs text-gray-400">${i.sku} | Stock: ${parseFloat(stock).toFixed(2)} ${i.unit?.symbol ?? ''} | PKR ${parseFloat(i.purchase_price).toFixed(2)}</p>
    </div>`;
  }).join('');
  $('#itemDropdown').html(html).removeClass('hidden');
});

$(document).on('click', '#itemDropdown [data-id]', function(){
  const id = $(this).data('id');
  const item = itemMap[id];
  if(!item) return;
  const wId = $('#warehouseSelect').val();
  const stock = item.inventory ? item.inventory.find(inv => inv.warehouse_id == wId)?.quantity ?? 0 : 0;
  WMS.addItemRow('itemsContainer', { id: item.id, name: item.name, sku: item.sku, unit: item.unit?.symbol, stock, selling_price: item.purchase_price, unit_cost: item.purchase_price });
  $('#emptyItems').hide();
  $('#itemSearch').val('');
  $('#itemDropdown').addClass('hidden');
});

$(document).on('click', function(e){
  if(!$(e.target).closest('#itemSearch, #itemDropdown').length) $('#itemDropdown').addClass('hidden');
});

$('#shippingInput').on('input', function(){ $('#shippingDisplay').text(parseFloat($(this).val()||0).toFixed(2)); WMS.recalcTotal(); });
</script>
@endpush
@endsection
