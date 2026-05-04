<?php
namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Models\Item;
use App\Models\Inventory;

class DashboardController extends Controller
{
    public function __construct(private ReportService $report) {}

    public function index()
    {
        $stats     = $this->report->getDashboardStats();
        $chartData = $this->report->getChartData();
        $topSuppliers = $this->report->getTopSuppliers(5);
        $topCustomers = $this->report->getTopCustomers(5);
        $lowStockItems = app(\App\Services\InventoryService::class)->getLowStockItems();
        return view('dashboard', compact('stats','chartData','topSuppliers','topCustomers','lowStockItems'));
    }
}
