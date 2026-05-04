<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  *{margin:0;padding:0;box-sizing:border-box} body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#1f2937;background:#fff}
  .header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;padding-bottom:16px;border-bottom:3px solid #2563eb}
  .logo{font-size:22px;font-weight:700;color:#2563eb} .logo span{color:#1f2937}
  .company-info{font-size:10px;color:#6b7280;margin-top:4px}
  .invoice-title{text-align:right}
  .invoice-title h2{font-size:24px;color:#1f2937;font-weight:700;letter-spacing:2px}
  .invoice-title .ref{font-size:14px;font-weight:600;color:#2563eb;margin-top:4px}
  .invoice-title .date{font-size:10px;color:#6b7280;margin-top:2px}
  .meta{display:flex;justify-content:space-between;margin-bottom:24px;gap:20px}
  .meta-block{flex:1}
  .meta-block .meta-label{font-size:9px;text-transform:uppercase;color:#6b7280;letter-spacing:.5px;margin-bottom:4px}
  .meta-block p{font-size:11px;color:#1f2937;line-height:1.6}
  .meta-block .name{font-weight:700;font-size:12px}
  table{width:100%;border-collapse:collapse;margin-bottom:16px}
  thead th{background:#1e40af;color:#fff;padding:9px 12px;text-align:left;font-size:10px;text-transform:uppercase}
  thead th:last-child,thead th:nth-last-child(2),thead th:nth-last-child(3){text-align:right}
  tbody td{padding:9px 12px;border-bottom:1px solid #f3f4f6;font-size:11px}
  tbody td:last-child,tbody td:nth-last-child(2),tbody td:nth-last-child(3){text-align:right}
  tbody tr:nth-child(even){background:#f9fafb}
  .totals-wrap{display:flex;justify-content:flex-end;margin-bottom:20px}
  .totals{width:260px}
  .totals .row{display:flex;justify-content:space-between;padding:5px 0;font-size:11px;border-bottom:1px solid #f3f4f6}
  .totals .row.grand{border-top:2px solid #2563eb;margin-top:4px;padding-top:8px;font-weight:700;font-size:14px;color:#2563eb;border-bottom:none}
  .totals .row.paid{color:#059669;border-bottom:none}
  .totals .row.due{color:#dc2626;font-weight:600;border-bottom:none}
  .payments-section{margin-bottom:20px}
  .payments-section h4{font-size:11px;font-weight:700;margin-bottom:8px;color:#1f2937}
  .status-badge{display:inline-block;padding:3px 10px;border-radius:999px;font-size:9px;font-weight:700}
  .badge-paid{background:#d1fae5;color:#065f46}
  .badge-partial{background:#fef3c7;color:#92400e}
  .badge-unpaid{background:#fee2e2;color:#991b1b}
  .footer{margin-top:32px;padding-top:12px;border-top:1px solid #e5e7eb;text-align:center;font-size:9px;color:#9ca3af}
  .watermark-paid{position:fixed;top:40%;left:25%;font-size:72px;font-weight:900;color:rgba(5,150,105,.08);transform:rotate(-35deg);letter-spacing:8px}
</style>
</head>
<body>
@if($sale->payment_status === 'paid')
<div class="watermark-paid">PAID</div>
@endif

<div class="header">
  <div>
    <div class="logo">WMS <span>Pro</span></div>
    <div class="company-info">Warehouse Management System</div>
  </div>
  <div class="invoice-title">
    <h2>INVOICE</h2>
    <div class="ref">{{ $sale->reference }}</div>
    <div class="date">Date: {{ $sale->sale_date->format('d M Y') }}</div>
    <div style="margin-top:6px">
      <span class="status-badge badge-{{ $sale->payment_status }}">{{ strtoupper($sale->payment_status) }}</span>
    </div>
  </div>
</div>

<div class="meta">
  <div class="meta-block">
    <div class="meta-label">Bill To</div>
    <p class="name">{{ $sale->customer->name }}</p>
    @if($sale->customer->phone)<p>{{ $sale->customer->phone }}</p>@endif
    @if($sale->customer->email)<p>{{ $sale->customer->email }}</p>@endif
    @if($sale->customer->address)<p>{{ $sale->customer->address }}</p>@endif
  </div>
  <div class="meta-block" style="text-align:right">
    <div class="meta-label">Ship From</div>
    <p class="name">{{ $sale->warehouse->name }}</p>
    @if($sale->warehouse->address)<p>{{ $sale->warehouse->address }}</p>@endif
    @if($sale->notes)
    <div style="margin-top:10px">
      <div class="meta-label">Notes</div>
      <p>{{ $sale->notes }}</p>
    </div>
    @endif
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Item</th>
      <th>SKU</th>
      <th style="text-align:right">Qty</th>
      <th style="text-align:right">Unit Price</th>
      <th style="text-align:right">Subtotal</th>
    </tr>
  </thead>
  <tbody>
    @foreach($sale->items as $i => $item)
    <tr>
      <td>{{ $i+1 }}</td>
      <td><strong>{{ $item->item->name }}</strong></td>
      <td style="color:#6b7280;font-size:9px;font-family:monospace">{{ $item->item->sku }}</td>
      <td style="text-align:right">{{ number_format($item->quantity,2) }}</td>
      <td style="text-align:right">PKR {{ number_format($item->unit_price,2) }}</td>
      <td style="text-align:right;font-weight:600">PKR {{ number_format($item->subtotal,2) }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="totals-wrap">
  <div class="totals">
    <div class="row"><span>Subtotal</span><span>PKR {{ number_format($sale->subtotal,2) }}</span></div>
    @if($sale->discount_amount > 0)<div class="row" style="color:#dc2626"><span>Discount</span><span>- PKR {{ number_format($sale->discount_amount,2) }}</span></div>@endif
    @if($sale->tax_amount > 0)<div class="row"><span>Tax</span><span>PKR {{ number_format($sale->tax_amount,2) }}</span></div>@endif
    @if($sale->shipping_cost > 0)<div class="row"><span>Shipping</span><span>PKR {{ number_format($sale->shipping_cost,2) }}</span></div>@endif
    <div class="row grand"><span>Grand Total</span><span>PKR {{ number_format($sale->total_amount,2) }}</span></div>
    <div class="row paid"><span>Amount Paid</span><span>PKR {{ number_format($sale->paid_amount,2) }}</span></div>
    <div class="row due"><span>Balance Due</span><span>PKR {{ number_format($sale->due_amount,2) }}</span></div>
  </div>
</div>

@if($sale->payments->count())
<div class="payments-section">
  <h4>Payment History</h4>
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Method</th>
        <th>Reference</th>
        <th style="text-align:right">Amount</th>
      </tr>
    </thead>
    <tbody>
      @foreach($sale->payments as $pay)
      <tr>
        <td>{{ $pay->payment_date->format('d M Y') }}</td>
        <td>{{ ucfirst($pay->payment_method) }}</td>
        <td>{{ $pay->reference ?? '—' }}</td>
        <td style="text-align:right;color:#059669;font-weight:600">PKR {{ number_format($pay->amount,2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

<div class="footer">
  Thank you for your business! | WMS Pro | Generated on {{ now()->format('d M Y H:i') }}
</div>
</body>
</html>
