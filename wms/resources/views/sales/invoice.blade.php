<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Invoice {{ $sale->reference }}</title>
  <style>
    *{margin:0;padding:0;box-sizing:border-box} body{font-family:sans-serif;font-size:12px;color:#1f2937;background:#fff}
    .header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;padding-bottom:16px;border-bottom:2px solid #2563eb}
    .logo{font-size:22px;font-weight:700;color:#2563eb} .logo span{color:#1f2937}
    .invoice-title{text-align:right} .invoice-title h2{font-size:18px;color:#2563eb;font-weight:700}
    .meta{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px}
    .meta-block label{font-size:10px;text-transform:uppercase;color:#6b7280;letter-spacing:.5px}
    .meta-block p{font-weight:600;margin-top:2px}
    table{width:100%;border-collapse:collapse;margin-bottom:16px}
    th{background:#f9fafb;padding:8px;text-align:left;font-size:10px;text-transform:uppercase;color:#6b7280;border-bottom:1px solid #e5e7eb}
    td{padding:8px;border-bottom:1px solid #f3f4f6}
    .totals{width:220px;margin-left:auto}
    .totals .row{display:flex;justify-content:space-between;padding:4px 0;font-size:11px}
    .totals .row.grand{border-top:2px solid #2563eb;margin-top:4px;padding-top:8px;font-weight:700;font-size:14px;color:#2563eb}
    .status-badges span{display:inline-block;padding:3px 10px;border-radius:9999px;font-size:10px;font-weight:600}
    .badge-success{background:#d1fae5;color:#065f46} .badge-warning{background:#fef3c7;color:#92400e} .badge-danger{background:#fee2e2;color:#991b1b}
    .footer{margin-top:32px;padding-top:16px;border-top:1px solid #e5e7eb;text-align:center;color:#9ca3af;font-size:10px}
    @media print{body{print-color-adjust:exact}}
  </style>
</head>
<body>
<div style="max-width:760px;margin:32px auto;padding:32px">
  <div class="header">
    <div>
      <div class="logo">WMS <span>Pro</span></div>
      <p style="color:#6b7280;font-size:11px;margin-top:4px">Warehouse Management System</p>
    </div>
    <div class="invoice-title">
      <h2>INVOICE</h2>
      <p style="font-size:13px;font-weight:600;margin-top:4px">{{ $sale->reference }}</p>
      <p style="color:#6b7280;margin-top:2px">{{ $sale->sale_date->format('d M Y') }}</p>
    </div>
  </div>

  <div class="meta">
    <div class="meta-block">
      <label>Bill To</label>
      <p>{{ $sale->customer->name }}</p>
      <p style="font-weight:400;color:#6b7280">{{ $sale->customer->phone }}</p>
      <p style="font-weight:400;color:#6b7280">{{ $sale->customer->address }}</p>
    </div>
    <div class="meta-block" style="text-align:right">
      <label>Warehouse</label><p>{{ $sale->warehouse->name }}</p>
      <label style="margin-top:8px;display:block">Payment Status</label>
      <div class="status-badges"><span class="{{ $sale->payment_status === 'paid' ? 'badge-success' : ($sale->payment_status === 'partial' ? 'badge-warning' : 'badge-danger') }}">{{ strtoupper($sale->payment_status) }}</span></div>
    </div>
  </div>

  <table>
    <thead><tr><th>#</th><th>Item</th><th>SKU</th><th style="text-align:right">Qty</th><th style="text-align:right">Price</th><th style="text-align:right">Subtotal</th></tr></thead>
    <tbody>
      @foreach($sale->items as $i => $item)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $item->item->name }}</td>
        <td style="color:#6b7280">{{ $item->item->sku }}</td>
        <td style="text-align:right">{{ number_format($item->quantity,2) }}</td>
        <td style="text-align:right">PKR {{ number_format($item->unit_price,2) }}</td>
        <td style="text-align:right;font-weight:600">PKR {{ number_format($item->subtotal,2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="totals">
    <div class="row"><span>Subtotal</span><span>PKR {{ number_format($sale->subtotal,2) }}</span></div>
    @if($sale->discount_amount > 0)<div class="row"><span>Discount</span><span style="color:#ef4444">- PKR {{ number_format($sale->discount_amount,2) }}</span></div>@endif
    @if($sale->tax_amount > 0)<div class="row"><span>Tax</span><span>PKR {{ number_format($sale->tax_amount,2) }}</span></div>@endif
    @if($sale->shipping_cost > 0)<div class="row"><span>Shipping</span><span>PKR {{ number_format($sale->shipping_cost,2) }}</span></div>@endif
    <div class="row grand"><span>Grand Total</span><span>PKR {{ number_format($sale->total_amount,2) }}</span></div>
    <div class="row" style="color:#10b981"><span>Paid</span><span>PKR {{ number_format($sale->paid_amount,2) }}</span></div>
    <div class="row" style="color:#ef4444;font-weight:600"><span>Due</span><span>PKR {{ number_format($sale->due_amount,2) }}</span></div>
  </div>

  @if($sale->notes)<p style="margin-top:20px;background:#f9fafb;padding:12px;border-radius:8px;color:#6b7280;font-size:11px"><strong>Notes:</strong> {{ $sale->notes }}</p>@endif

  <div class="footer">
    <p>Thank you for your business! &mdash; WMS Pro &mdash; Generated {{ now()->format('d M Y H:i') }}</p>
  </div>
</div>
<script>window.onload = () => window.print();</script>
</body>
</html>
