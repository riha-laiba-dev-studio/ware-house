@extends('layouts.app')
@section('title','New Purchase')
@section('page-title','Create Purchase Order')

@section('content')
<form method="POST" action="{{ route('purchases.store') }}" id="purchaseForm">
  @csrf
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="lg:col-span-2 space-y-4">
      <div class="card">
        <div class="card-header">
          <h3 class="font-semibold text-gray-700">Purchase Details</h3>
        </div>
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
          <div class="flex items-center gap-2 flex-wrap">
            <div class="relative">
              <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
              <input type="text" id="itemSearch" placeholder="Search item or scan barcode..." class="form-input pl-8 w-64 text-sm">
              <div id="itemDropdown" class="absolute top-full left-0 w-80 bg-white border border-gray-200 rounded-lg shadow-xl z-50 hidden max-h-56 overflow-y-auto"></div>
            </div>
            <button type="button" id="scannerToggleBtn" onclick="toggleScanner()" class="btn-outline btn-sm" title="Open barcode scanner">
              <i class="fas fa-barcode text-blue-500"></i> Scan
            </button>
          </div>
        </div>

        {{-- Barcode Scanner --}}
        <div id="scannerContainer" class="hidden border-t border-gray-100">
          <div class="p-4 bg-gray-50">
            <div class="flex items-center justify-between mb-3">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                <p class="text-sm font-medium text-gray-700">Barcode Scanner — Point camera at barcode</p>
              </div>
              <button type="button" onclick="toggleScanner()" class="text-gray-400 hover:text-gray-600 text-xs"><i class="fas fa-times mr-1"></i>Close</button>
            </div>
            <div class="relative bg-black rounded-xl overflow-hidden" style="height:240px">
              <video id="scannerVideo" class="w-full h-full object-cover" playsinline></video>
              <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="w-56 h-32 border-2 border-blue-400 rounded-lg relative">
                  <div class="absolute top-0 left-0 w-5 h-5 border-t-4 border-l-4 border-blue-400 rounded-tl"></div>
                  <div class="absolute top-0 right-0 w-5 h-5 border-t-4 border-r-4 border-blue-400 rounded-tr"></div>
                  <div class="absolute bottom-0 left-0 w-5 h-5 border-b-4 border-l-4 border-blue-400 rounded-bl"></div>
                  <div class="absolute bottom-0 right-0 w-5 h-5 border-b-4 border-r-4 border-blue-400 rounded-br"></div>
                  <div id="scanLine" class="absolute left-2 right-2 h-0.5 bg-blue-400 opacity-80" style="top:50%;animation:scan 2s linear infinite"></div>
                </div>
              </div>
            </div>
            <p id="scannerStatus" class="text-xs text-center text-gray-500 mt-2">Starting camera...</p>
            <p class="text-xs text-center text-gray-400 mt-1"><i class="fas fa-keyboard mr-1"></i>USB barcode scanners work automatically via keyboard input</p>
          </div>
        </div>

        <div class="overflow-x-auto">
          <table class="table">
            <thead>
              <tr>
                <th>Item</th>
                <th>Unit</th>
                <th>Available</th>
                <th>Qty</th>
                <th>Unit Cost</th>
                <th>Subtotal</th>
                <th></th>
              </tr>
            </thead>
            <tbody id="itemsContainer"></tbody>
          </table>
          <div id="emptyItems" class="text-center py-6 text-gray-400 text-sm"><i class="fas fa-search mb-2 block text-2xl"></i>Search and add items above</div>
        </div>
      </div>
    </div>

    <div class="space-y-4">
      <div class="card">
        <div class="card-header">
          <h3 class="font-semibold text-gray-700">Order Summary</h3>
        </div>
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
document.addEventListener('DOMContentLoaded', function () {
// Preloaded items (no dependency on API calls)
const items = @json($items);
if (!Array.isArray(items)) {
  console.error('Purchase dropdown items payload is not an array:', items);
}
const itemMap = {};
items.forEach(i => { itemMap[i.id] = i; });
let searchResults = [];

function renderDropdown(list) {
  if (!list.length) {
    $('#itemDropdown').addClass('hidden').html('');
    return;
  }

  const warehouseId = $('#warehouseSelect').val();

  const html = list.map(i => {
    const stock = i.inventory
      ? (i.inventory.find(inv => inv.warehouse_id == warehouseId)?.quantity ?? 0)
      : 0;

    return `
      <div class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b"
           data-id="${i.id}">
        <p class="text-sm font-medium text-gray-800">${i.name}</p>
        <p class="text-xs text-gray-400">
          ${(i.sku || '')} | Stock: ${parseFloat(stock).toFixed(2)} ${i.unit?.symbol || ''} | PKR ${parseFloat(i.purchase_price || 0).toFixed(2)}
        </p>
      </div>
    `;
  }).join('');

  $('#itemDropdown').html(html).removeClass('hidden');
}

function getFilteredItems(q) {
  const query = (q || '').toLowerCase().trim();
  const filtered = items.filter(i => {
    return (i.name || '').toLowerCase().includes(query)
      || (i.sku || '').toLowerCase().includes(query)
      || (i.barcode || '').toLowerCase().includes(query);
  });

  // Keep it snappy
  return filtered.slice(0, 15);
}

// Show suggestions on focus (so the "item list" is visible immediately)
$('#itemSearch').on('focus click', function () {
  const q = $(this).val();
  const list = q && q.length >= 1 ? getFilteredItems(q) : items.slice().sort((a,b) => (a.name||'').localeCompare(b.name||'')) .slice(0, 15);
  searchResults = list;
  renderDropdown(list);
});

// 🔍 SEARCH ITEM (local filter)
$('#itemSearch').on('input', function () {
  const q = $(this).val();

  if (!q || q.length < 1) {
    $('#itemDropdown').addClass('hidden').html('');
    return;
  }

  if (q.length < 2) {
    // Still allow results for 1 character to meet your "list visible" requirement.
    const list = getFilteredItems(q);
    searchResults = list;
    renderDropdown(list);
    return;
  }

  const list = getFilteredItems(q);
  searchResults = list;
  renderDropdown(list);
});


// ✅ CLICK ITEM → ADD TO TABLE
$(document).on('click', '#itemDropdown [data-id]', function () {

  const id = $(this).data('id'); // ✅ FIX
  const item = itemMap[id] || searchResults.find(i => i.id == id); // ✅ FIX

  if (!item) return;

  const warehouseId = $('#warehouseSelect').val();
  const stock = item.inventory
    ? (item.inventory.find(inv => inv.warehouse_id == warehouseId)?.quantity ?? 0)
    : 0;

  WMS.addItemRow('itemsContainer', {
    id: item.id,
    name: item.name,
    sku: item.sku,
    unit: item.unit?.symbol || '',
    stock: stock,
    selling_price: item.purchase_price,
    unit_cost: item.purchase_price
  });

  $('#emptyItems').hide();
  $('#itemSearch').val('');
  $('#itemDropdown').addClass('hidden');
});


// 🔽 CLOSE DROPDOWN
$(document).on('click', function (e) {
  if (!$(e.target).closest('#itemSearch, #itemDropdown').length) {
    $('#itemDropdown').addClass('hidden');
  }
});


// 🚚 SHIPPING UPDATE
$('#shippingInput').on('input', function () {
  $('#shippingDisplay').text(parseFloat($(this).val() || 0).toFixed(2));
  WMS.recalcTotal();
});
});
</script>
<style>
  @keyframes scan {

    0%,
    100% {
      top: 10%
    }

    50% {
      top: 90%
    }
  }
</style>
@endpush
@endsection