<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\InventoryAdjustmentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;

Route::get('/login', [LoginController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware(['auth', 'log.activity'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('warehouses', WarehouseController::class);
    Route::resource('items', ItemController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);

    Route::resource('purchases', PurchaseController::class)->except(['edit','update','destroy']);
    Route::post('purchases/{purchase}/receive',  [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::post('purchases/{purchase}/payment',  [PurchaseController::class, 'addPayment'])->name('purchases.payment');

    Route::resource('sales', SaleController::class)->except(['edit','update','destroy']);
    Route::post('sales/{sale}/payment', [SaleController::class, 'addPayment'])->name('sales.payment');
    Route::get('sales/{sale}/invoice',  [SaleController::class, 'invoice'])->name('sales.invoice');

    Route::resource('stock-transfers', StockTransferController::class)->except(['edit','update','destroy']);
    Route::post('stock-transfers/{stockTransfer}/approve', [StockTransferController::class, 'approve'])->name('stock-transfers.approve');

    Route::resource('inventory-adjustments', InventoryAdjustmentController::class)->except(['edit','update','destroy']);

    Route::resource('expenses', ExpenseController::class)->except(['edit','update']);

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('profit-loss',  [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('sales',        [ReportController::class, 'sales'])->name('sales');
        Route::get('purchases',    [ReportController::class, 'purchases'])->name('purchases');
        Route::get('stock',        [ReportController::class, 'stock'])->name('stock');
        Route::get('open-balance', [ReportController::class, 'openBalanceSheet'])->name('open-balance');
    });

    Route::middleware('role:Admin')->group(function () {
        Route::resource('users', UserController::class);
    });
});
