<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  *{margin:0;padding:0;box-sizing:border-box} body{font-family:DejaVu Sans,sans-serif;font-size:12px;color:#1f2937}
  .header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:28px;padding-bottom:16px;border-bottom:2px solid #7c3aed}
  .logo{font-size:22px;font-weight:700;color:#7c3aed} .logo span{color:#1f2937}
  .title{text-align:right} .title h2{font-size:18px;color:#1f2937;font-weight:700} .title p{font-size:11px;color:#6b7280;margin-top:4px}
  .cards{display:flex;flex-wrap:wrap;gap:14px;margin-bottom:24px}
  .card{background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:16px 20px;flex:1;min-width:160px}
  .card .label{font-size:10px;text-transform:uppercase;color:#6b7280;letter-spacing:.5px}
  .card .value{font-size:18px;font-weight:700;margin-top:4px}
  .section{background:#fff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;margin-bottom:20px}
  .section-header{background:#f9fafb;padding:12px 16px;font-weight:600;font-size:12px;border-bottom:1px solid #e5e7eb}
  table{width:100%;border-collapse:collapse}
  table td{padding:12px 16px;border-bottom:1px solid #f3f4f6}
  table .label-col{color:#6b7280;width:60%}
  table .value-col{font-weight:600;text-align:right}
  .grand{background:#ede9fe;font-size:16px}
  .footer{margin-top:28px;text-align:center;font-size:10px;color:#9ca3af;border-top:1px solid #e5e7eb;padding-top:12px}
</style>
</head>
<body>
<div class="header">
  <div>
    <div class="logo">WMS <span>Pro</span></div>
    <div style="font-size:10px;color:#6b7280;margin-top:4px">Warehouse Management System</div>
  </div>
  <div class="title">
    <h2>Profit & Loss Statement</h2>
    <p>Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</p>
    <p style="margin-top:2px">Generated: {{ now()->format('d M Y H:i') }}</p>
  </div>
</div>

<div class="section">
  <div class="section-header">Income</div>
  <table>
    <tr><td class="label-col">Total Sales Revenue</td><td class="value-col" style="color:#2563eb">PKR {{ number_format($data['sales'],2) }}</td></tr>
  </table>
</div>

<div class="section">
  <div class="section-header">Cost of Goods Sold (COGS)</div>
  <table>
    <tr><td class="label-col">Cost of Goods Sold</td><td class="value-col" style="color:#dc2626">PKR {{ number_format($data['costOfGoods'],2) }}</td></tr>
    <tr style="background:#f0fdf4"><td class="label-col font-semibold"><strong>Gross Profit</strong></td><td class="value-col" style="color:{{ $data['grossProfit'] >= 0 ? '#059669' : '#dc2626' }}"><strong>PKR {{ number_format($data['grossProfit'],2) }}</strong></td></tr>
  </table>
</div>

<div class="section">
  <div class="section-header">Operating Expenses</div>
  <table>
    <tr><td class="label-col">Total Expenses</td><td class="value-col" style="color:#d97706">PKR {{ number_format($data['expenses'],2) }}</td></tr>
  </table>
</div>

<div class="section">
  <div class="section-header">Summary</div>
  <table>
    <tr><td class="label-col">Gross Profit</td><td class="value-col">PKR {{ number_format($data['grossProfit'],2) }}</td></tr>
    <tr><td class="label-col">Less: Operating Expenses</td><td class="value-col" style="color:#dc2626">- PKR {{ number_format($data['expenses'],2) }}</td></tr>
    <tr class="grand">
      <td class="label-col"><strong>Net Profit / (Loss)</strong></td>
      <td class="value-col" style="color:{{ $data['netProfit'] >= 0 ? '#059669' : '#dc2626' }}"><strong>PKR {{ number_format($data['netProfit'],2) }}</strong></td>
    </tr>
  </table>
</div>

<div class="footer">WMS Pro — Profit & Loss Statement | {{ now()->format('d M Y H:i') }}</div>
</body>
</html>
