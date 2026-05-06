@extends('layouts.app')
@section('title','New Adjustment')
@section('page-title','Inventory Adjustment')

@section('content')
<form method="POST" action="{{ route('inventory-adjustments.store') }}">
@csrf
<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-4">
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Adjustment Details</h3></div>
      <div class="card-body grid grid-cols-2 md:grid-cols-3 gap-4">
        <div><label class="form-label">Warehouse *</label>
          <select name="warehouse_id" id="warehouseSelect" class="form-select" required>
            <option value="">Select</option>
            @foreach($warehouses as $w)<option value="{{ $w->id }}">{{ $w->name }}</option>@endforeach
          </select>
        </div>
        <div><label class="form-label">Date *</label><input type="date" name="adjustment_date" value="{{ date('Y-m-d') }}" class="form-input" required></div>
        <div><label class="form-label">Type *</label>
          <select name="type" class="form-select" required>
            <option value="manual">Manual Adjustment</option><option value="damage">Damage</option><option value="loss">Loss</option><option value="found">Found</option>
          </select>
        </div>
        <div class="md:col-span-3"><label class="form-label">Notes</label><textarea name="notes" rows="2" class="form-input"></textarea></div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3 class="font-semibold text-gray-700">Items</h3>
        <div class="relative"><input type="text" id="itemSearch" placeholder="Search items..." class="form-input w-56 text-sm">
          <div id="itemDropdown" class="absolute top-full left-0 w-72 bg-white border border-gray-200 rounded-lg shadow-xl z-50 hidden max-h-56 overflow-y-auto"></div>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="table"><thead><tr><th>Item</th><th>Current Stock</th><th>New Quantity</th><th>Reason</th><th></th></tr></thead>
          <tbody id="itemsContainer"></tbody>
        </table>
        <div id="emptyItems" class="text-center py-6 text-gray-400 text-sm">Search and add items to adjust</div>
      </div>
    </div>
  </div>
  <div class="space-y-4">
    <button type="submit" class="btn-warning w-full justify-center py-3"><i class="fas fa-sliders"></i> Apply Adjustment</button>
    <a href="{{ route('inventory-adjustments.index') }}" class="btn-outline w-full justify-center">Cancel</a>
    <div class="card p-4 bg-amber-50 border border-amber-200">
      <p class="text-xs text-amber-700"><i class="fas fa-triangle-exclamation mr-1"></i> Adjustments are applied immediately and will update stock levels. This action cannot be undone.</p>
    </div>
  </div>
</div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
const items = @json($items);
const itemMap = {};
items.forEach(i => { itemMap[i.id] = i; });

$('#itemSearch').on('input', function(){
  const q = $(this).val().toLowerCase();
  if(q.length < 2){ $('#itemDropdown').addClass('hidden'); return; }
  const wId = parseInt($('#warehouseSelect').val());
  const filtered = items.filter(i => i.name.toLowerCase().includes(q));
  if(!filtered.length){ $('#itemDropdown').addClass('hidden'); return; }
  const html = filtered.slice(0,10).map(i => {
    const stock = i.inventory ? (i.inventory.find(inv => inv.warehouse_id == wId)?.quantity ?? 0) : 0;
    return `<div class="px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-50" data-id="${i.id}" data-stock="${stock}">
      <p class="text-sm font-medium">${i.name}</p>
      <p class="text-xs text-gray-400">${i.sku} | Current: ${parseFloat(stock).toFixed(2)}</p>
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
    <td class="font-semibold text-blue-600">${parseFloat(stock).toFixed(2)}</td>
    <td><input type="number" name="items[${idx}][adjusted_quantity]" class="form-input w-28" min="0" step="0.01" value="${stock}" required></td>
    <td><input type="text" name="items[${idx}][reason]" class="form-input w-36" placeholder="Reason..."></td>
    <td><button type="button" class="text-red-500 hover:text-red-700 remove-row">&#10005;</button></td>
  </tr>`;
  $('#itemsContainer').append(row);
  $('#emptyItems').hide();
  $('#itemSearch').val(''); $('#itemDropdown').addClass('hidden');
  $('.remove-row').off('click').on('click', function(){ $(this).closest('tr').remove(); });
});
$(document).on('click', function(e){ if(!$(e.target).closest('#itemSearch,#itemDropdown').length) $('#itemDropdown').addClass('hidden'); });
});
</script>
@endpush
@endsection
