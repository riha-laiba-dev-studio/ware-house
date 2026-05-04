<?php
namespace App\Services;

use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Expense;
use App\Models\Inventory;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDashboardStats(): array
    {
        $today = today();
        $thisMonth = [now()->startOfMonth(), now()->endOfMonth()];

        $totalSales      = Sale::whereBetween('sale_date', $thisMonth)->where('status','confirmed')->sum('total_amount');
        $totalPurchases  = Purchase::whereBetween('purchase_date', $thisMonth)->sum('total_amount');
        $totalExpenses   = Expense::whereBetween('expense_date', $thisMonth)->sum('amount');
        $totalProfit     = $this->calculateProfit($thisMonth[0], $thisMonth[1]);
        $todaySales      = Sale::whereDate('sale_date', $today)->where('status','confirmed')->sum('total_amount');
        $todayPurchases  = Purchase::whereDate('purchase_date', $today)->sum('total_amount');
        $lowStockCount   = Inventory::whereRaw('quantity <= (SELECT alert_quantity FROM items WHERE items.id = inventory.item_id)')->count();
        $totalItemsValue = Inventory::join('items','items.id','=','inventory.item_id')->sum(DB::raw('inventory.quantity * items.purchase_price'));

        return compact('totalSales','totalPurchases','totalExpenses','totalProfit','todaySales','todayPurchases','lowStockCount','totalItemsValue');
    }

    public function getChartData(string $period = 'monthly'): array
    {
        $months = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $label = $date->format('M Y');
            $months[$label] = [
                'sales'     => Sale::whereYear('sale_date',$date->year)->whereMonth('sale_date',$date->month)->where('status','confirmed')->sum('total_amount'),
                'purchases' => Purchase::whereYear('purchase_date',$date->year)->whereMonth('purchase_date',$date->month)->sum('total_amount'),
                'expenses'  => Expense::whereYear('expense_date',$date->year)->whereMonth('expense_date',$date->month)->sum('amount'),
                'profit'    => $this->calculateProfit($date->startOfMonth()->toDateString(),$date->endOfMonth()->toDateString()),
            ];
        }
        return $months;
    }

    public function getTopSuppliers(int $limit = 5): array
    {
        return Purchase::with('supplier')
            ->select('supplier_id', DB::raw('SUM(total_amount) as total_payable'), DB::raw('SUM(paid_amount) as total_paid'), DB::raw('SUM(due_amount) as total_due'))
            ->groupBy('supplier_id')
            ->orderByDesc('total_payable')
            ->limit($limit)
            ->get()
            ->map(fn($p) => ['supplier'=>$p->supplier,'total_payable'=>$p->total_payable,'total_paid'=>$p->total_paid,'total_due'=>$p->total_due])
            ->toArray();
    }

    public function getTopCustomers(int $limit = 5): array
    {
        return Sale::with('customer')
            ->where('status','confirmed')
            ->select('customer_id', DB::raw('SUM(total_amount) as total_payable'), DB::raw('SUM(paid_amount) as total_paid'), DB::raw('SUM(due_amount) as total_due'))
            ->groupBy('customer_id')
            ->orderByDesc('total_payable')
            ->limit($limit)
            ->get()
            ->map(fn($s) => ['customer'=>$s->customer,'total_payable'=>$s->total_payable,'total_paid'=>$s->total_paid,'total_due'=>$s->total_due])
            ->toArray();
    }

    public function getProfitLossReport(string $from, string $to): array
    {
        $sales      = Sale::whereBetween('sale_date',[$from,$to])->where('status','confirmed')->sum('total_amount');
        $purchases  = Purchase::whereBetween('purchase_date',[$from,$to])->sum('total_amount');
        $expenses   = Expense::whereBetween('expense_date',[$from,$to])->sum('amount');
        $costOfGoods= SaleItem::whereHas('sale',fn($q)=>$q->whereBetween('sale_date',[$from,$to])->where('status','confirmed'))->sum(DB::raw('quantity * purchase_price'));
        $grossProfit = $sales - $costOfGoods;
        $netProfit   = $grossProfit - $expenses;
        return compact('sales','purchases','expenses','costOfGoods','grossProfit','netProfit');
    }

    public function getSalesReport(string $from, string $to, ?int $warehouseId = null, ?int $customerId = null)
    {
        $query = Sale::with(['customer','warehouse','items.item'])->whereBetween('sale_date',[$from,$to])->where('status','confirmed');
        if ($warehouseId) $query->where('warehouse_id',$warehouseId);
        if ($customerId)  $query->where('customer_id',$customerId);
        return $query->orderByDesc('sale_date')->get();
    }

    public function getPurchasesReport(string $from, string $to, ?int $warehouseId = null, ?int $supplierId = null)
    {
        $query = Purchase::with(['supplier','warehouse','items.item'])->whereBetween('purchase_date',[$from,$to]);
        if ($warehouseId) $query->where('warehouse_id',$warehouseId);
        if ($supplierId)  $query->where('supplier_id',$supplierId);
        return $query->orderByDesc('purchase_date')->get();
    }

    public function getStockReport(?int $warehouseId = null)
    {
        $query = Inventory::with(['item.category','item.unit','warehouse'])
            ->join('items','items.id','=','inventory.item_id')
            ->select('inventory.*', DB::raw('inventory.quantity * items.purchase_price as stock_value'));
        if ($warehouseId) $query->where('inventory.warehouse_id',$warehouseId);
        return $query->get();
    }

    private function calculateProfit(string $from, string $to): float
    {
        $revenue = Sale::whereBetween('sale_date',[$from,$to])->where('status','confirmed')->sum('total_amount');
        $cogs    = SaleItem::whereHas('sale',fn($q)=>$q->whereBetween('sale_date',[$from,$to])->where('status','confirmed'))->sum(DB::raw('quantity * purchase_price'));
        $expenses= Expense::whereBetween('expense_date',[$from,$to])->sum('amount');
        return $revenue - $cogs - $expenses;
    }
}
