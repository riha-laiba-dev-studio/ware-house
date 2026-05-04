<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  *{margin:0;padding:0;box-sizing:border-box} body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#1f2937}
  .header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;padding-bottom:14px;border-bottom:2px solid #0f766e}
  .logo{font-size:20px;font-weight:700;color:#0f766e} .logo span{color:#1f2937}
  .title{text-align:right} .title h2{font-size:16px;font-weight:700} .title p{font-size:10px;color:#6b7280;margin-top:3px}
  h3{font-size:13px;font-weight:700;margin:20px 0 10px;padding-bottom:6px;border-bottom:1px solid #e5e7eb}
  table{width:100%;border-collapse:collapse;font-size:10px;margin-bottom:16px}
  thead th{background:#f9fafb;padding:8px 10px;text-align:left;font-size:9px;text-transform:uppercase;color:#6b7280;border-bottom:2px solid #e5e7eb}
  tbody td{padding:8px 10px;border-bottom:1px solid #f3f4f6}
  tfoot td{background:#f9fafb;font-weight:700;padding:8px 10px;border-top:2px solid #e5e7eb}
  .footer{margin-top:20px;text-align:center;font-size:9px;color:#9ca3af;border-top:1px solid #e5e7eb;padding-top:10px}
</style>
</head>
<body>
<div class="header">
  <div>
    <div class="logo">WMS <span>Pro</span></div>
    <div style="font-size:9px;color:#6b7280;margin-top:4px">Warehouse Management System</div>
  </div>
  <div class="title">
    <h2>Open Balance Sheet</h2>
    <p>Generated: {{ now()->format('d M Y H:i') }}</p>
  </div>
</div>

<h3>Supplier Balances (Payables)</h3>
<table>
  <thead>
    <tr><th>#</th><th>Supplier</th><th>Total Purchases</th><th>Total Paid</th><th>Outstanding Due</th></tr>
  </thead>
  <tbody>
    @foreach($suppliers as $i => $s)
    <tr>
      <td>{{ $i+1 }}</td>
      <td><strong>{{ $s->name }}</strong></td>
      <td>PKR {{ number_format($s->purchases_sum_total_amount ?? 0, 2) }}</td>
      <td style="color:#059669">PKR {{ number_format($s->purchases_sum_paid_amount ?? 0, 2) }}</td>
      <td style="color:{{ ($s->purchases_sum_due_amount ?? 0) > 0 ? '#dc2626' : '#059669' }};font-weight:600">PKR {{ number_format($s->purchases_sum_due_amount ?? 0, 2) }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2">Totals</td>
      <td>PKR {{ number_format($suppliers->sum('purchases_sum_total_amount'), 2) }}</td>
      <td style="color:#059669">PKR {{ number_format($suppliers->sum('purchases_sum_paid_amount'), 2) }}</td>
      <td style="color:#dc2626">PKR {{ number_format($suppliers->sum('purchases_sum_due_amount'), 2) }}</td>
    </tr>
  </tfoot>
</table>

<h3>Customer Balances (Receivables)</h3>
<table>
  <thead>
    <tr><th>#</th><th>Customer</th><th>Total Sales</th><th>Total Paid</th><th>Outstanding Due</th></tr>
  </thead>
  <tbody>
    @foreach($customers as $i => $c)
    <tr>
      <td>{{ $i+1 }}</td>
      <td><strong>{{ $c->name }}</strong></td>
      <td>PKR {{ number_format($c->sales_sum_total_amount ?? 0, 2) }}</td>
      <td style="color:#059669">PKR {{ number_format($c->sales_sum_paid_amount ?? 0, 2) }}</td>
      <td style="color:{{ ($c->sales_sum_due_amount ?? 0) > 0 ? '#dc2626' : '#059669' }};font-weight:600">PKR {{ number_format($c->sales_sum_due_amount ?? 0, 2) }}</td>
    </tr>
    @endforeach
  </tbody>
  <tfoot>
    <tr>
      <td colspan="2">Totals</td>
      <td>PKR {{ number_format($customers->sum('sales_sum_total_amount'), 2) }}</td>
      <td style="color:#059669">PKR {{ number_format($customers->sum('sales_sum_paid_amount'), 2) }}</td>
      <td style="color:#dc2626">PKR {{ number_format($customers->sum('sales_sum_due_amount'), 2) }}</td>
    </tr>
  </tfoot>
</table>

<div class="footer">WMS Pro — Open Balance Sheet | {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
