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

// ---- Barcode Scanner (must be global because of inline onclick="toggleScanner()") ----
let scannerStream = null;
let barcodeDetector = null;
let scannerActive = false;

window.toggleScanner = function toggleScanner() {
  const container = document.getElementById('scannerContainer');
  if (scannerActive) {
    closeScanner();
    container.classList.add('hidden');
    document.getElementById('scannerToggleBtn').innerHTML = '<i class="fas fa-barcode text-blue-500"></i> Scan';
  } else {
    container.classList.remove('hidden');
    document.getElementById('scannerToggleBtn').innerHTML = '<i class="fas fa-times text-red-500"></i> Close';
    startScanner();
  }
};

async function startScanner() {
  const video = document.getElementById('scannerVideo');
  const status = document.getElementById('scannerStatus');
  try {
    if ('BarcodeDetector' in window) {
      barcodeDetector = new BarcodeDetector({ formats: ['code_128','code_39','ean_13','ean_8','qr_code','upc_a','upc_e','codabar','itf'] });
    }
    scannerStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } } });
    video.srcObject = scannerStream;
    video.play();
    scannerActive = true;
    status.textContent = barcodeDetector ? 'Camera ready — aim at barcode' : 'Camera ready (manual entry active)';
    if (barcodeDetector) scanFrame(video);
  } catch(e) {
    status.textContent = 'Camera unavailable — type SKU in search or use USB scanner';
    status.className = 'text-xs text-center text-amber-600 mt-2';
  }
}

async function scanFrame(video) {
  if (!scannerActive || !barcodeDetector) return;
  try {
    const codes = await barcodeDetector.detect(video);
    if (codes.length > 0) {
      const sku = codes[0].rawValue;
      document.getElementById('scannerStatus').textContent = '✓ Scanned: ' + sku;
      searchItemBySku(sku);
      await new Promise(r => setTimeout(r, 1800));
    }
  } catch(e) {}
  if (scannerActive) requestAnimationFrame(() => scanFrame(video));
}

function closeScanner() {
  scannerActive = false;
  if (scannerStream) { scannerStream.getTracks().forEach(t => t.stop()); scannerStream = null; }
}

function searchItemBySku(sku) {
  const wId = parseInt($('#warehouseSelect').val());
  const item = items.find(i => i.sku === sku || i.sku.toLowerCase() === sku.toLowerCase());
  if (item) {
    const stock = item.inventory ? (item.inventory.find(inv => inv.warehouse_id == wId)?.quantity ?? 0) : 0;
    WMS.addItemRow('itemsContainer', { id: item.id, name: item.name, sku: item.sku, unit: item.unit?.symbol, stock, selling_price: item.selling_price });
    $('#emptyItems').hide();
    const flash = document.createElement('div');
    flash.className = 'fixed top-4 right-4 z-50 bg-emerald-500 text-white text-sm px-4 py-2 rounded-lg shadow-lg';
    flash.textContent = '✓ Added: ' + item.name;
    document.body.appendChild(flash);
    setTimeout(() => flash.remove(), 2000);
  } else {
    $('#itemSearch').val(sku).trigger('input');
    document.getElementById('scannerStatus').textContent = 'SKU not found: ' + sku;
  }
}

// USB scanner support
let usbBuffer = '', usbTimer = null;

// Bind jQuery-dependent handlers after Vite bundle loads (so `$` exists)
document.addEventListener('DOMContentLoaded', function () {
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

  $(document).on('keydown', function(e) {
    if ($(e.target).is('input,textarea,select')) return;
    if (e.key === 'Enter') {
      if (usbBuffer.length >= 4) searchItemBySku(usbBuffer);
      usbBuffer = ''; clearTimeout(usbTimer); return;
    }
    if (e.key.length === 1) {
      usbBuffer += e.key;
      clearTimeout(usbTimer);
      usbTimer = setTimeout(() => { usbBuffer = ''; }, 100);
    }
  });
});
</script>
<style>
@keyframes scan { 0%,100%{top:10%} 50%{top:90%} }
</style>
@endpush
@endsection
