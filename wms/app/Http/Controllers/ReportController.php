<?php
namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Customer;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function __construct(private ReportService $report) {}

    public function profitLoss(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();
        $data = $this->report->getProfitLossReport($from, $to);
        $chartData = $this->report->getChartData();
        return view('reports.profit-loss', compact('data','from','to','chartData'));
    }

    public function profitLossPdf(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();
        $data = $this->report->getProfitLossReport($from, $to);
        $pdf = Pdf::loadView('exports.profit-loss-pdf', compact('data','from','to'))->setPaper('a4');
        return $pdf->download("profit-loss-{$from}-to-{$to}.pdf");
    }

    public function sales(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();
        $sales = $this->report->getSalesReport($from, $to, $request->warehouse_id, $request->customer_id);
        $warehouses = Warehouse::active()->get();
        $customers  = Customer::active()->get();
        $total = $sales->sum('total_amount');
        $totalPaid = $sales->sum('paid_amount');
        $totalDue  = $sales->sum('due_amount');
        return view('reports.sales', compact('sales','from','to','warehouses','customers','total','totalPaid','totalDue'));
    }

    public function salesCsv(Request $request)
    {
        $from  = $request->from ?? now()->startOfMonth()->toDateString();
        $to    = $request->to   ?? now()->toDateString();
        $sales = $this->report->getSalesReport($from, $to, $request->warehouse_id, $request->customer_id);

        return new StreamedResponse(function () use ($sales) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Reference','Customer','Date','Items','Subtotal','Total','Paid','Due','Payment Status']);
            foreach ($sales as $s) {
                fputcsv($handle, [
                    $s->reference, $s->customer->name, $s->sale_date->format('d-m-Y'),
                    $s->items->count(), $s->subtotal, $s->total_amount,
                    $s->paid_amount, $s->due_amount, ucfirst($s->payment_status)
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sales-report-'.$from.'-to-'.$to.'.csv"',
        ]);
    }

    public function salesPdf(Request $request)
    {
        $from  = $request->from ?? now()->startOfMonth()->toDateString();
        $to    = $request->to   ?? now()->toDateString();
        $sales = $this->report->getSalesReport($from, $to, $request->warehouse_id, $request->customer_id);
        $total = $sales->sum('total_amount');
        $totalPaid = $sales->sum('paid_amount');
        $totalDue  = $sales->sum('due_amount');
        $pdf = Pdf::loadView('exports.sales-pdf', compact('sales','from','to','total','totalPaid','totalDue'))->setPaper('a4','landscape');
        return $pdf->download("sales-report-{$from}-to-{$to}.pdf");
    }

    public function purchases(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();
        $purchases  = $this->report->getPurchasesReport($from, $to, $request->warehouse_id, $request->supplier_id);
        $warehouses = Warehouse::active()->get();
        $suppliers  = Supplier::active()->get();
        $total = $purchases->sum('total_amount');
        $totalPaid = $purchases->sum('paid_amount');
        $totalDue  = $purchases->sum('due_amount');
        return view('reports.purchases', compact('purchases','from','to','warehouses','suppliers','total','totalPaid','totalDue'));
    }

    public function purchasesCsv(Request $request)
    {
        $from      = $request->from ?? now()->startOfMonth()->toDateString();
        $to        = $request->to   ?? now()->toDateString();
        $purchases = $this->report->getPurchasesReport($from, $to, $request->warehouse_id, $request->supplier_id);

        return new StreamedResponse(function () use ($purchases) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Reference','Supplier','Date','Total','Paid','Due','Status','Payment']);
            foreach ($purchases as $p) {
                fputcsv($handle, [
                    $p->reference, $p->supplier->name, $p->purchase_date->format('d-m-Y'),
                    $p->total_amount, $p->paid_amount, $p->due_amount,
                    ucfirst($p->status), ucfirst($p->payment_status)
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="purchases-report-'.$from.'-to-'.$to.'.csv"',
        ]);
    }

    public function purchasesPdf(Request $request)
    {
        $from      = $request->from ?? now()->startOfMonth()->toDateString();
        $to        = $request->to   ?? now()->toDateString();
        $purchases = $this->report->getPurchasesReport($from, $to, $request->warehouse_id, $request->supplier_id);
        $total = $purchases->sum('total_amount');
        $totalPaid = $purchases->sum('paid_amount');
        $totalDue  = $purchases->sum('due_amount');
        $pdf = Pdf::loadView('exports.purchases-pdf', compact('purchases','from','to','total','totalPaid','totalDue'))->setPaper('a4','landscape');
        return $pdf->download("purchases-report-{$from}-to-{$to}.pdf");
    }

    public function stock(Request $request)
    {
        $stock      = $this->report->getStockReport($request->warehouse_id);
        $warehouses = Warehouse::active()->get();
        $totalValue = $stock->sum('stock_value');
        $lowStock   = $stock->filter(fn($s) => $s->quantity <= $s->item->alert_quantity)->count();
        return view('reports.stock', compact('stock','warehouses','totalValue','lowStock'));
    }

    public function stockCsv(Request $request)
    {
        $stock = $this->report->getStockReport($request->warehouse_id);

        return new StreamedResponse(function () use ($stock) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Item','SKU','Category','Warehouse','Unit','Quantity','Alert Qty','Cost Price','Stock Value','Status']);
            foreach ($stock as $s) {
                fputcsv($handle, [
                    $s->item->name, $s->item->sku, $s->item->category->name,
                    $s->warehouse->name, $s->item->unit->symbol,
                    $s->quantity, $s->item->alert_quantity,
                    $s->item->purchase_price, $s->stock_value,
                    $s->quantity <= $s->item->alert_quantity ? 'Low Stock' : 'OK'
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stock-report-'.date('d-m-Y').'.csv"',
        ]);
    }

    public function stockPdf(Request $request)
    {
        $stock      = $this->report->getStockReport($request->warehouse_id);
        $warehouses = Warehouse::active()->get();
        $totalValue = $stock->sum('stock_value');
        $warehouseLabel = $request->warehouse_id
            ? $warehouses->firstWhere('id', $request->warehouse_id)?->name ?? 'All'
            : 'All Warehouses';
        $pdf = Pdf::loadView('exports.stock-pdf', compact('stock','totalValue','warehouseLabel'))->setPaper('a4','landscape');
        return $pdf->download("stock-report-".date('d-m-Y').".pdf");
    }

    public function openBalanceSheet(Request $request)
    {
        $suppliers = \App\Models\Supplier::withSum(['purchases'=>fn($q)=>$q],'total_amount')
            ->withSum(['purchases'=>fn($q)=>$q],'paid_amount')
            ->withSum(['purchases'=>fn($q)=>$q],'due_amount')->get();
        $customers = \App\Models\Customer::withSum(['sales'=>fn($q)=>$q->where('status','confirmed')],'total_amount')
            ->withSum(['sales'=>fn($q)=>$q->where('status','confirmed')],'paid_amount')
            ->withSum(['sales'=>fn($q)=>$q->where('status','confirmed')],'due_amount')->get();
        return view('reports.open-balance', compact('suppliers','customers'));
    }

    public function openBalancePdf()
    {
        $suppliers = \App\Models\Supplier::withSum(['purchases'=>fn($q)=>$q],'total_amount')
            ->withSum(['purchases'=>fn($q)=>$q],'paid_amount')
            ->withSum(['purchases'=>fn($q)=>$q],'due_amount')->get();
        $customers = \App\Models\Customer::withSum(['sales'=>fn($q)=>$q->where('status','confirmed')],'total_amount')
            ->withSum(['sales'=>fn($q)=>$q->where('status','confirmed')],'paid_amount')
            ->withSum(['sales'=>fn($q)=>$q->where('status','confirmed')],'due_amount')->get();
        $pdf = Pdf::loadView('exports.open-balance-pdf', compact('suppliers','customers'))->setPaper('a4');
        return $pdf->download("open-balance-sheet-".date('d-m-Y').".pdf");
    }

    public function saleInvoicePdf(\App\Models\Sale $sale)
    {
        $sale->load(['customer','warehouse','items.item','payments']);
        $pdf = Pdf::loadView('exports.sale-invoice-pdf', compact('sale'))->setPaper('a4');
        return $pdf->download("invoice-{$sale->reference}.pdf");
    }

    public function purchaseOrderPdf(\App\Models\Purchase $purchase)
    {
        $purchase->load(['supplier','warehouse','items.item','payments']);
        $pdf = Pdf::loadView('exports.purchase-order-pdf', compact('purchase'))->setPaper('a4');
        return $pdf->download("po-{$purchase->reference}.pdf");
    }
}
