<?php
namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Models\Warehouse;
use App\Models\Supplier;
use App\Models\Customer;
use Illuminate\Http\Request;

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

    public function sales(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();
        $sales = $this->report->getSalesReport($from, $to, $request->warehouse_id, $request->customer_id);
        $warehouses = Warehouse::active()->get();
        $customers  = Customer::active()->get();
        $total = $sales->sum('total_amount');
        return view('reports.sales', compact('sales','from','to','warehouses','customers','total'));
    }

    public function purchases(Request $request)
    {
        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to   = $request->to   ?? now()->toDateString();
        $purchases  = $this->report->getPurchasesReport($from, $to, $request->warehouse_id, $request->supplier_id);
        $warehouses = Warehouse::active()->get();
        $suppliers  = Supplier::active()->get();
        $total = $purchases->sum('total_amount');
        return view('reports.purchases', compact('purchases','from','to','warehouses','suppliers','total'));
    }

    public function stock(Request $request)
    {
        $stock      = $this->report->getStockReport($request->warehouse_id);
        $warehouses = Warehouse::active()->get();
        $totalValue = $stock->sum('stock_value');
        return view('reports.stock', compact('stock','warehouses','totalValue'));
    }

    public function openBalanceSheet(Request $request)
    {
        $suppliers = \App\Models\Supplier::withSum(['purchases'=>fn($q)=>$q],'total_amount')->withSum(['purchases'=>fn($q)=>$q],'paid_amount')->withSum(['purchases'=>fn($q)=>$q],'due_amount')->get();
        $customers = \App\Models\Customer::withSum(['sales'=>fn($q)=>$q->where('status','confirmed')],'total_amount')->withSum(['sales'=>fn($q)=>$q->where('status','confirmed')],'paid_amount')->withSum(['sales'=>fn($q)=>$q->where('status','confirmed')],'due_amount')->get();
        return view('reports.open-balance', compact('suppliers','customers'));
    }
}
