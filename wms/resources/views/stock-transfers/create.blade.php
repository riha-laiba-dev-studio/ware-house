@extends('layouts.app')
@section('title','New Transfer')
@section('page-title','Create Stock Transfer')

@section('content')
<form method="POST" action="{{ route('stock-transfers.store') }}">
@csrf
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-4">
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Transfer Details</h3></div>
      <div class="card-body grid grid-cols-2 md:grid-cols-3 gap-4">
        <div><label class="form-label">From Warehouse *</label>
          <select name="from_warehouse_id" id="fromWarehouse" class="form-select" required>
            <option value="">Select</option>
            @foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="form-label">To Warehouse *</label>
          <select name="to_warehouse_id" class="form-select" required>
            <option value="">Select</option>
            @foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="form-label">Transfer Date *</label><input type="date" name="transfer_date" value="{{ date('Y-m-d') }}" class="form-input" required></div>
        <div class="md:col-span-3"><label class="form-label">Notes</label><input type="text" name="notes" class="form-input"></div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3 class="font-semibold text-gray-700">Items to Transfer</h3>
        <div class="relative"><input type="text" id="itemSearch" placeholder="Search items..." class="form-input w-56 text-sm">
          <div id="itemDropdown" class="absolute top-full left-0 w-72 bg-white border border-gray-200 rounded-lg shadow-xl z-50 hidden max-h-56 overflow-y-auto"></div>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="table"><thead><tr><th>Item</th><th>Unit</th><th>Available</th><th>Qty to Transfer</th><th></th></tr></thead>
          <tbody id="itemsContainer"></tbody>
        </table>
        <div id="emptyItems" class="text-center py-6 text-gray-400 text-sm"><i class="fas fa-right-left text-2xl mb-2 block"></i>Search and add items to transfer</div>
      </div>
    </div>
  </div>
  <div class="space-y-4">
    <button type="submit" class="btn-primary w-full justify-center py-3"><i class="fas fa-right-left"></i> Create Transfer</button>
    <a href="{{ route('stock-transfers.index') }}" class="btn-outline w-full justify-center">Cancel</a>
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
  const wId = parseInt($('#fromWarehouse').val());
  const filtered = items.filter(i => i.name.toLowerCase().includes(q) || (i.sku||'').toLowerCase().includes(q));
  if(!filtered.length){ $('#itemDropdown').addClass('hidden'); return; }
  let html = filtered.slice(0,10).map(i => {
    const stock = i.inventory ? (i.inventory.find(inv => inv.warehouse_id == wId)?.quantity ?? 0) : 0;
    return `<div class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-50" data-id="${i.id}" data-stock="${stock}">
      <p class="text-sm font-medium">${i.name}</p>
      <p class="text-xs text-gray-400">${i.sku} | Available: <span class="${stock<=0?'text-red-500':'text-emerald-500'}">${parseFloat(stock).toFixed(2)}</span></p>
    </div>`;
  }).join('');
  $('#itemDropdown').html(html).removeClass('hidden');
});

$(document).on('click', '#itemDropdown [data-id]', function(){
  const id = $(this).data('id');
  const item = itemMap[id];
  const stock = $(this).data('stock');
  const idx = $('#itemsContainer tr').length;
  const row = `<tr>
    <td>${item.name}<input type="hidden" name="items[${idx}][item_id]" value="${item.id}"></td>
    <td>${item.unit?.symbol||''}</td>
    <td><span class="${stock<=0?'text-red-500':'text-emerald-600'} font-semibold">${parseFloat(stock).toFixed(2)}</span></td>
    <td><input type="number" name="items[${idx}][quantity]" class="form-input w-28" min="0.01" max="${stock}" step="0.01" value="1" required></td>
    <td><button type="button" class="text-red-500 hover:text-red-700 remove-row">&#10005;</button></td>
  </tr>`;
  $('#itemsContainer').append(row);
  $('#emptyItems').hide();
  $('#itemSearch').val(''); $('#itemDropdown').addClass('hidden');
  $('.remove-row').off('click').on('click', function(){ $(this).closest('tr').remove(); });
});
$(document).on('click', function(e){ if(!$(e.target).closest('#itemSearch,#itemDropdown').length) $('#itemDropdown').addClass('hidden'); });
</script>
@endpush
@endsection
