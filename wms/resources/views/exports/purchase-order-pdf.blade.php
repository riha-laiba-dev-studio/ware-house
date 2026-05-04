<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  *{margin:0;padding:0;box-sizing:border-box} body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#1f2937;background:#fff}
  .header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;padding-bottom:16px;border-bottom:3px solid #059669}
  .logo{font-size:22px;font-weight:700;color:#059669} .logo span{color:#1f2937}
  .po-title{text-align:right}
  .po-title h2{font-size:24px;color:#1f2937;font-weight:700;letter-spacing:2px}
  .po-title .ref{font-size:14px;font-weight:600;color:#059669;margin-top:4px}
  .po-title .date{font-size:10px;color:#6b7280;margin-top:2px}
  .meta{display:flex;justify-content:space-between;margin-bottom:24px;gap:20px}
  .meta-block{flex:1}
  .meta-block .meta-label{font-size:9px;text-transform:uppercase;color:#6b7280;letter-spacing:.5px;margin-bottom:4px}
  .meta-block .name{font-weight:700;font-size:12px}
  table{width:100%;border-collapse:collapse;margin-bottom:16px}
  thead th{background:#065f46;color:#fff;padding:9px 12px;text-align:left;font-size:10px;text-transform:uppercase}
  thead th:last-child,thead th:nth-last-child(2),thead th:nth-last-child(3){text-align:right}
  tbody td{padding:9px 12px;border-bottom:1px solid #f3f4f6}
  tbody td:last-child,tbody td:nth-last-child(2){text-align:right}
  tbody tr:nth-child(even){background:#f9fafb}
  .totals-wrap{display:flex;justify-content:flex-end;margin-bottom:20px}
  .totals{width:240px}
  .totals .row{display:flex;justify-content:space-between;padding:5px 0;font-size:11px;border-bottom:1px solid #f3f4f6}
  .totals .row.grand{border-top:2px solid #059669;margin-top:4px;padding-top:8px;font-weight:700;font-size:14px;color:#059669;border-bottom:none}
  .status-badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:9px;font-weight:700}
  .badge-received{background:#d1fae5;color:#065f46}
  .badge-pending{background:#fef3c7;color:#92400e}
  .footer{margin-top:32px;padding-top:12px;border-top:1px solid #e5e7eb;text-align:center;font-size:9px;color:#9ca3af}
</style>
</head>
<body>
<div class="header">
  <div>
    <div class="logo">WMS <span>Pro</span></div>
    <div style="font-size:10px;color:#6b7280;margin-top:4px">Warehouse Management System</div>
  </div>
  <div class="po-title">
    <h2>PURCHASE ORDER</h2>
    <div class="ref">{{ $purchase->reference }}</div>
    <div class="date">Date: {{ $purchase->purchase_date->format('d M Y') }}</div>
    <div style="margin-top:6px">
      <span class="status-badge badge-{{ $purchase->status }}">{{ strtoupper($purchase->status) }}</span>
    </div>
  </div>
</div>

<div class="meta">
  <div class="meta-block">
    <div class="meta-label">Supplier</div>
    <p class="name">{{ $purchase->supplier->name }}</p>
    @if($purchase->supplier->phone)<p>{{ $purchase->supplier->phone }}</p>@endif
    @if($purchase->supplier->email)<p>{{ $purchase->supplier->email }}</p>@endif
    @if($purchase->supplier->address)<p>{{ $purchase->supplier->address }}</p>@endif
  </div>
  <div class="meta-block" style="text-align:right">
    <div class="meta-label">Deliver To</div>
    <p class="name">{{ $purchase->warehouse->name }}</p>
    @if($purchase->warehouse->address)<p>{{ $purchase->warehouse->address }}</p>@endif
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Item</th>
      <th>SKU</th>
      <th style="text-align:center">Ordered</th>
      <th style="text-align:center">Received</th>
      <th style="text-align:right">Unit Cost</th>
      <th style="text-align:right">Subtotal</th>
    </tr>
  </thead>
  <tbody>
    @foreach($purchase->items as $i => $item)
    <tr>
      <td>{{ $i+1 }}</td>
      <td><strong>{{ $item->item->name }}</strong></td>
      <td style="color:#6b7280;font-size:9px;font-family:monospace">{{ $item->item->sku }}</td>
      <td style="text-align:center">{{ number_format($item->quantity,2) }}</td>
      <td style="text-align:center;color:{{ $item->received_quantity >= $item->quantity ? '#059669' : '#d97706' }}">{{ number_format($item->received_quantity,2) }}</td>
      <td style="text-align:right">PKR {{ number_format($item->unit_cost,2) }}</td>
      <td style="text-align:right;font-weight:600">PKR {{ number_format($item->subtotal,2) }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="totals-wrap">
  <div class="totals">
    <div class="row"><span>Subtotal</span><span>PKR {{ number_format($purchase->subtotal,2) }}</span></div>
    @if($purchase->shipping_cost > 0)<div class="row"><span>Shipping</span><span>PKR {{ number_format($purchase->shipping_cost,2) }}</span></div>@endif
    <div class="row grand"><span>Grand Total</span><span>PKR {{ number_format($purchase->total_amount,2) }}</span></div>
    <div class="row" style="color:#059669;border-bottom:none"><span>Paid</span><span>PKR {{ number_format($purchase->paid_amount,2) }}</span></div>
    <div class="row" style="color:#dc2626;font-weight:600;border-bottom:none"><span>Due</span><span>PKR {{ number_format($purchase->due_amount,2) }}</span></div>
  </div>
</div>

@if($purchase->notes)
<p style="background:#f9fafb;border-radius:6px;padding:10px 14px;font-size:10px;color:#6b7280;margin-bottom:16px"><strong>Notes:</strong> {{ $purchase->notes }}</p>
@endif

<div class="footer">
  WMS Pro — Purchase Order | Generated on {{ now()->format('d M Y H:i') }}
</div>
</body>
</html>
