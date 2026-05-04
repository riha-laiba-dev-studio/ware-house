<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  *{margin:0;padding:0;box-sizing:border-box} body{font-family:DejaVu Sans,sans-serif;font-size:11px;color:#1f2937}
  .header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;padding-bottom:14px;border-bottom:2px solid #2563eb}
  .logo{font-size:18px;font-weight:700;color:#2563eb} .logo span{color:#1f2937}
  .title{text-align:right} .title h2{font-size:16px;color:#1f2937;font-weight:700} .title p{font-size:10px;color:#6b7280;margin-top:3px}
  .summary-grid{display:flex;gap:12px;margin-bottom:16px}
  .summary-card{flex:1;background:#f9fafb;border:1px solid #e5e7eb;border-radius:6px;padding:10px 14px}
  .summary-card .label{font-size:9px;text-transform:uppercase;color:#6b7280;letter-spacing:.5px}
  .summary-card .value{font-size:14px;font-weight:700;margin-top:3px}
  table{width:100%;border-collapse:collapse;font-size:10px}
  thead th{background:#1e40af;color:#fff;padding:8px 10px;text-align:left;font-size:9px;text-transform:uppercase}
  tbody td{padding:7px 10px;border-bottom:1px solid #f3f4f6}
  tbody tr:nth-child(even){background:#f9fafb}
  .badge-success{background:#d1fae5;color:#065f46;padding:1px 6px;border-radius:999px;font-size:9px}
  .badge-danger{background:#fee2e2;color:#991b1b;padding:1px 6px;border-radius:999px;font-size:9px}
  .badge-warning{background:#fef3c7;color:#92400e;padding:1px 6px;border-radius:999px;font-size:9px}
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
    <h2>Sales Report</h2>
    <p>Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
    <p>Generated: {{ now()->format('d M Y H:i') }}</p>
  </div>
</div>

<div class="summary-grid">
  <div class="summary-card">
    <div class="label">Total Invoices</div>
    <div class="value" style="color:#2563eb">{{ $sales->count() }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Total Revenue</div>
    <div class="value">PKR {{ number_format($total, 2) }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Total Paid</div>
    <div class="value" style="color:#059669">PKR {{ number_format($totalPaid, 2) }}</div>
  </div>
  <div class="summary-card">
    <div class="label">Total Due</div>
    <div class="value" style="color:#dc2626">PKR {{ number_format($totalDue, 2) }}</div>
  </div>
</div>

<table>
  <thead>
    <tr>
      <th>#</th>
      <th>Reference</th>
      <th>Customer</th>
      <th>Date</th>
      <th>Items</th>
      <th>Total</th>
      <th>Paid</th>
      <th>Due</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
    @forelse($sales as $i => $s)
    <tr>
      <td>{{ $i+1 }}</td>
      <td style="font-family:monospace;color:#2563eb">{{ $s->reference }}</td>
      <td>{{ $s->customer->name }}</td>
      <td>{{ $s->sale_date->format('d M Y') }}</td>
      <td style="text-align:center">{{ $s->items->count() }}</td>
      <td style="font-weight:600">PKR {{ number_format($s->total_amount,2) }}</td>
      <td style="color:#059669">PKR {{ number_format($s->paid_amount,2) }}</td>
      <td style="color:#dc2626">PKR {{ number_format($s->due_amount,2) }}</td>
      <td><span class="badge-{{ $s->payment_status==='paid'?'success':($s->payment_status==='partial'?'warning':'danger') }}">{{ ucfirst($s->payment_status) }}</span></td>
    </tr>
    @empty
    <tr><td colspan="9" style="text-align:center;padding:20px;color:#9ca3af">No records found</td></tr>
    @endforelse
  </tbody>
  <tfoot>
    <tr>
      <td colspan="5">Totals</td>
      <td>PKR {{ number_format($total,2) }}</td>
      <td style="color:#059669">PKR {{ number_format($totalPaid,2) }}</td>
      <td style="color:#dc2626">PKR {{ number_format($totalDue,2) }}</td>
      <td></td>
    </tr>
  </tfoot>
</table>

<div class="footer">WMS Pro — Sales Report | {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
