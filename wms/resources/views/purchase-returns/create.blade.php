@extends('layouts.app')
@section('title','New Purchase Return')
@section('page-title','New Purchase Return')

@section('content')
<form method="POST" action="{{ route('purchase-returns.store') }}" id="returnForm">
@csrf
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
  <div class="lg:col-span-2 space-y-5">
    <div class="card">
      <div class="card-header"><h3 class="font-semibold text-gray-700">Return Details</h3></div>
      <div class="card-body grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="form-label">Purchase Order *</label>
          <select name="purchase_id" id="poSelect" class="form-select" required>
            <option value="">Select purchase order...</option>
            @foreach($purchases as $p)
            <option value="{{ $p->id }}">{{ $p->reference }} — {{ $p->supplier->name }} ({{ $p->purchase_date->format('d M Y') }})</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="form-label">Return Date *</label>
          <input type="date" name="return_date" class="form-input" value="{{ date('Y-m-d') }}" required>
        </div>
        <div class="md:col-span-2">
          <label class="form-label">Reason for Return</label>
          <textarea name="reason" class="form-input" rows="2" placeholder="Wrong item, damaged goods, over-supply, etc."></textarea>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3 class="font-semibold text-gray-700">Items to Return</h3>
        <span class="text-xs text-gray-400">Select a PO above to load items</span>
      </div>
      <div id="itemsWrap" class="hidden">
        <div class="overflow-x-auto">
          <table class="table">
            <thead><tr><th>Item</th><th>Unit</th><th>Return Qty</th><th>Unit Cost</th><th>Subtotal</th><th></th></tr></thead>
            <tbody id="itemsBody"></tbody>
          </table>
        </div>
      </div>
      <div id="noItems" class="py-10 text-center text-gray-400 text-sm">
        <i class="fas fa-arrow-up text-2xl mb-2 block opacity-30"></i>Select a purchase order to see its items
      </div>
    </div>
  </div>

  <div class="space-y-4">
    <div class="card">
      <div class="card-body space-y-3 text-sm">
        <div class="flex justify-between items-center py-2 border-b border-gray-100">
          <span class="text-gray-500">Return Total</span>
          <span class="text-xl font-bold text-orange-700" id="totalDisplay">PKR 0.00</span>
        </div>
        <div class="bg-orange-50 rounded-lg p-3 text-xs text-orange-800">
          <i class="fas fa-info-circle mr-1"></i>Stock will be automatically <strong>deducted</strong> upon saving this return.
        </div>
      </div>
    </div>
    <button type="submit" class="btn-warning w-full justify-center py-3"><i class="fas fa-rotate-right"></i> Process Return</button>
    <a href="{{ route('purchase-returns.index') }}" class="btn-outline w-full justify-center"><i class="fas fa-arrow-left"></i> Back</a>
  </div>
</div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  $('#poSelect').on('change', function(){
    const id = $(this).val();
    if(!id){ $('#itemsWrap').addClass('hidden'); $('#noItems').removeClass('hidden'); return; }
    $.get('/purchase-returns/'+id+'/items', function(po){
      let html = '';
      po.items.forEach((item,i) => {
        html += `<tr>
          <td><strong>${item.item.name}</strong><br><span class="text-xs text-gray-400">${item.item.sku}</span></td>
          <td>${item.item.unit?.symbol??''}</td>
          <td><input type="number" name="items[${i}][quantity]" class="form-input w-24 qty-input" min="0.01" max="${item.received_quantity}" step="0.01" value="${item.received_quantity}">
            <input type="hidden" name="items[${i}][item_id]" value="${item.item_id}">
            <p class="text-xs text-gray-400 mt-0.5">Max: ${parseFloat(item.received_quantity).toFixed(2)}</p></td>
          <td><input type="number" name="items[${i}][unit_cost]" class="form-input w-28 price-input" step="0.01" min="0" value="${item.unit_cost}"></td>
          <td class="font-semibold subtotal-col">PKR ${(item.received_quantity*item.unit_cost).toFixed(2)}</td>
          <td><button type="button" onclick="$(this).closest('tr').remove(); window.calcTotal();" class="text-red-400 hover:text-red-600"><i class="fas fa-times"></i></button></td>
        </tr>`;
      });
      $('#itemsBody').html(html);
      $('#itemsWrap').removeClass('hidden');
      $('#noItems').addClass('hidden');
      window.calcTotal();
      $('.qty-input,.price-input').on('input', window.calcTotal);
    });
  });
});

window.calcTotal = function calcTotal(){
  let total = 0;
  $('#itemsBody tr').each(function(){
    const qty = parseFloat($(this).find('.qty-input').val()||0);
    const price = parseFloat($(this).find('.price-input').val()||0);
    const sub = qty * price;
    total += sub;
    $(this).find('.subtotal-col').text('PKR ' + sub.toFixed(2));
  });
  $('#totalDisplay').text('PKR ' + total.toFixed(2));
};
</script>
@endpush
@endsection
