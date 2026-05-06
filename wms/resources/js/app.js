import './bootstrap';
import $ from 'jquery';
window.$ = window.jQuery = $;

// Global AJAX setup
$.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// Auto-dismiss alerts
$(document).ready(function () {
  setTimeout(() => $('.auto-dismiss').fadeOut(500, function(){ $(this).remove(); }), 4000);

  // Sidebar toggle
  $('#sidebarToggle').on('click', function () {
    $('#sidebar').toggleClass('-translate-x-full');
    $('#sidebarOverlay').toggleClass('hidden');
  });
  $('#sidebarOverlay').on('click', function () {
    $('#sidebar').addClass('-translate-x-full');
    $(this).addClass('hidden');
  });

  // Confirm delete
  $('[data-confirm]').on('click', function (e) {
    if (!confirm($(this).data('confirm') || 'Are you sure?')) e.preventDefault();
  });
});

// Item row management for purchase/sale forms
window.WMS = {
  itemRows: [],

  addItemRow(containerId, itemData) {
    const container = $(`#${containerId}`);
    const idx = container.children().length;
    const initialPrice = parseFloat(itemData.selling_price || itemData.unit_cost || 0) || 0;
    const row = `
      <tr class="item-row">
        <td class="px-3 py-2">${itemData.name} <small class="text-gray-400">(${itemData.sku})</small><input type="hidden" name="items[${idx}][item_id]" value="${itemData.id}"></td>
        <td class="px-3 py-2">${itemData.unit || ''}</td>
        <td class="px-3 py-2 text-right text-blue-600 font-medium stock-col">${parseFloat(itemData.stock || 0).toFixed(2)}</td>
        <td class="px-3 py-2"><input type="number" name="items[${idx}][quantity]" class="form-input qty-input w-24" min="0.01" step="0.01" value="1" required></td>
        <td class="px-3 py-2">
          <input type="number" name="items[${idx}][unit_price]" class="form-input price-input w-28" min="0" step="0.01" value="${initialPrice}" required>
          <input type="hidden" name="items[${idx}][unit_cost]" class="unit-cost-hidden" value="${initialPrice}">
        </td>
        <td class="px-3 py-2 text-right subtotal-col font-semibold">${initialPrice.toFixed(2)}</td>
        <td class="px-3 py-2 text-center"><button type="button" class="text-red-500 hover:text-red-700 remove-row">&#10005;</button></td>
      </tr>`;
    container.append(row);
    WMS.bindRowEvents();
    WMS.recalcTotal();
  },

  bindRowEvents() {
    $('.item-row').off('input.wms').on('input.wms', '.qty-input, .price-input', function () {
      const row = $(this).closest('tr');
      const qty = parseFloat(row.find('.qty-input').val()) || 0;
      const price = parseFloat(row.find('.price-input').val()) || 0;
      row.find('.subtotal-col').text((qty * price).toFixed(2));
      // Purchase orders validate `unit_cost`; keep it synced with the visible unit_price input.
      row.find('.unit-cost-hidden').val(price);
      WMS.recalcTotal();
    });
    $('.remove-row').off('click.wms').on('click.wms', function () {
      $(this).closest('tr').remove();
      WMS.recalcTotal();
      WMS.renumberRows();
    });
  },

  recalcTotal() {
    let total = 0;
    $('.subtotal-col').each(function () { total += parseFloat($(this).text()) || 0; });
    $('#subtotalDisplay').text(total.toFixed(2));
    const disc = parseFloat($('#discountInput').val()) || 0;
    const tax  = parseFloat($('#taxInput').val()) || 0;
    const ship = parseFloat($('#shippingInput').val()) || 0;
    const grand = total - disc + tax + ship;
    $('#grandTotalDisplay').text(grand.toFixed(2));
  },

  renumberRows() {
    $('#itemsContainer tr').each(function (i) {
      $(this).find('[name*="items["]').each(function () {
        this.name = this.name.replace(/items\[\d+\]/, `items[${i}]`);
      });
    });
  },

  searchItem(query, warehouseId, callback) {
    if (query.length < 2) return;
    const url = window.WMS_CONFIG?.itemSearchUrl || '/ajax/items/search';
    $.get(url, { q: query, warehouse_id: warehouseId }, callback);
  }
};
