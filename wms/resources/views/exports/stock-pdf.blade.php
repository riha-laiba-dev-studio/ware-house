<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  *{margin:0;padding:0;box-sizing:border-box} body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#1f2937}
  .header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;padding-bottom:14px;border-bottom:2px solid #7c3aed}
  .logo{font-size:18px;font-weight:700;color:#7c3aed} .logo span{color:#1f2937}
  .title{text-align:right} .title h2{font-size:16px;color:#1f2937;font-weight:700} .title p{font-size:10px;color:#6b7280;margin-top:3px}
  .summary-grid{display:flex;gap:12px;margin-bottom:16px}
  .summary-card{flex:1;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:10px 14px}
  .summary-card .label{font-size:9px;text-transform:uppercase;color:#6b7280;letter-spacing:.5px}
  .summary-card .value{font-size:14px;font-weight:700;margin-top:3px}
  table{width:100%;border-collapse:collapse;font-size:10px}
  thead th{background:#4c1d95;color:#fff;padding:8px 10px;text-align:left;font-size:9px;text-transform:uppercase}
  tbody td{padding:7px 10px;border-bottom:1px solid #f3f4f6}
  tbody tr:nth-child(even){background:#f9fafb}
  .badge-success{background:#d1fae5;color:#065f46;padding:1px 6px;border-radius:999px;font-size:9px}
  .badge-danger{background:#fee2e2;color:#991b1b;padding:1px 6px;border-radius:999px;font-size:9px}
  tfoot td{background:#f9fafb;font-weight:700;padding:8px 10px;border-top:2px solid #e5e7eb}
  .footer{margin-top:20px;text-align:center;font-size:9px;color:#9ca3af;border-top:1px solid #e5e7eb;padding-top:10px}
</style>
</head>
<body>
<div class="header">
  <div>
    <div class="logo">WMS <span>Pro</span></div>
    <div style="font-size:9px;color:#6b7280;margin-top:3px">Warehouse Management System</div>
  </div>
  <div class="title">
    <h2>Stock Valuation Report</h2>
    <p>Warehouse: {{ $warehouseLabel }}</p>
    <p>Generated: {{ now()->format('d M Y H:i') }}</p>
  </div>
</div>

<div class="summary-grid">
  <div class="summary-card">
    <div class="label">Total SKUs</div>
    <div class="value" style="color:#7c3aed">{{ $stock->count() }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Low Stock Items</div>
    <div class="value" style="color:#dc2626">{{ $stock->filter(fn($s) => $s->quantity <= $s->item->alert_quantity)->count() }}</div>
  </div>
  <div class="summary-card" style="flex:2">
    <div class="label">Total Stock Value</div>
    <div class="value" style="color:#059669">PKR {{ number_format($totalValue,2) }}</div>
  </div>
</div>

<table>
  <thead>
    <tr><th>#</th><th>Item</th><th>SKU</th><th>Category</th><th>Warehouse</th><th>Unit</th><th>Qty</th><th>Alert</th><th>Cost Price</th><th>Stock Value</th><th>Status</th></tr>
  </thead>
  <tbody>
    @forelse($stock as $i => $s)
    <tr>
      <td>{{ $i+1 }}</td>
      <td><strong>{{ $s->item->name }}</strong></td>
      <td style="font-family:monospace;color:#7c3aed;font-size:9px">{{ $s->item->sku }}</td>
      <td>{{ $s->item->category->name }}</td>
      <td>{{ $s->warehouse->name }}</td>
      <td>{{ $s->item->unit->symbol }}</td>
      <td style="{{ $s->quantity <= $s->item->alert_quantity ? 'color:#dc2626;font-weight:600' : '' }}">{{ number_format($s->quantity,2) }}</td>
      <td>{{ $s->item->alert_quantity }}</td>
      <td>PKR {{ number_format($s->item->purchase_price,2) }}</td>
      <td style="font-weight:600;color:#059669">PKR {{ number_format($s->stock_value,2) }}</td>
      <td><span class="{{ $s->quantity <= $s->item->alert_quantity ? 'badge-danger' : 'badge-success' }}">{{ $s->quantity <= $s->item->alert_quantity ? 'Low' : 'OK' }}</span></td>
    </tr>
    @empty
    <tr><td colspan="11" style="text-align:center;padding:20px;color:#9ca3af">No stock data</td></tr>
    @endforelse
  </tbody>
  <tfoot>
    <tr>
      <td colspan="9" style="text-align:right;padding-right:10px">Total Stock Value:</td>
      <td style="color:#059669">PKR {{ number_format($totalValue,2) }}</td>
      <td></td>
    </tr>
  </tfoot>
</table>

<div class="footer">WMS Pro — Stock Report | {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
