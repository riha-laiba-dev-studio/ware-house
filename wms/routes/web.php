<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\InventoryMovementController;
use App\Http\Controllers\LoginLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\Api\ItemController as ApiItemController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProfileController;

Route::get('/offline', fn() => view('offline'))->name('offline');
Route::get('/login', [LoginController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Auth - Registration & Password reset (public/guest)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::middleware(['auth', 'log.activity'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    Route::resource('warehouses', WarehouseController::class);
    Route::resource('items', ItemController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);

    Route::resource('purchases', PurchaseController::class)->except(['edit', 'update', 'destroy']);
    Route::post('purchases/{purchase}/receive',  [PurchaseController::class, 'receive'])->name('purchases.receive');
    Route::post('purchases/{purchase}/payment',  [PurchaseController::class, 'addPayment'])->name('purchases.payment');

    Route::resource('sales', SaleController::class)->except(['edit', 'update', 'destroy']);
    Route::post('sales/{sale}/payment', [SaleController::class, 'addPayment'])->name('sales.payment');
    Route::get('sales/{sale}/invoice',  [SaleController::class, 'invoice'])->name('sales.invoice');

    Route::resource('stock-transfers', StockTransferController::class)->except(['edit', 'update', 'destroy']);
    Route::post('stock-transfers/{stockTransfer}/approve', [StockTransferController::class, 'approve'])->name('stock-transfers.approve');

    Route::resource('inventory-adjustments', InventoryAdjustmentController::class)->except(['edit', 'update', 'destroy']);

    Route::resource('expenses', ExpenseController::class)->except(['edit', 'update']);

    // Reports — view
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('profit-loss',  [ReportController::class, 'profitLoss'])->name('profit-loss');
        Route::get('sales',        [ReportController::class, 'sales'])->name('sales');
        Route::get('purchases',    [ReportController::class, 'purchases'])->name('purchases');
        Route::get('stock',        [ReportController::class, 'stock'])->name('stock');
        Route::get('open-balance', [ReportController::class, 'openBalanceSheet'])->name('open-balance');

        // Export — CSV
        Route::get('sales/csv',      [ReportController::class, 'salesCsv'])->name('sales.csv');
        Route::get('purchases/csv',  [ReportController::class, 'purchasesCsv'])->name('purchases.csv');
        Route::get('stock/csv',      [ReportController::class, 'stockCsv'])->name('stock.csv');

        // Export — PDF
        Route::get('sales/pdf',         [ReportController::class, 'salesPdf'])->name('sales.pdf');
        Route::get('purchases/pdf',     [ReportController::class, 'purchasesPdf'])->name('purchases.pdf');
        Route::get('stock/pdf',         [ReportController::class, 'stockPdf'])->name('stock.pdf');
        Route::get('profit-loss/pdf',   [ReportController::class, 'profitLossPdf'])->name('profit-loss.pdf');
        Route::get('open-balance/pdf',  [ReportController::class, 'openBalancePdf'])->name('open-balance.pdf');

        // Per-document PDF
        Route::get('sale/{sale}/invoice-pdf',       [ReportController::class, 'saleInvoicePdf'])->name('sale-invoice-pdf');
        Route::get('purchase/{purchase}/order-pdf', [ReportController::class, 'purchaseOrderPdf'])->name('purchase-order-pdf');
    });

    // Notifications / Alerts
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',                [NotificationController::class, 'index'])->name('index');
        Route::post('send-low-stock',  [NotificationController::class, 'sendLowStockEmail'])->name('send-low-stock');
        Route::post('send-payment-due', [NotificationController::class, 'sendPaymentDueEmail'])->name('send-payment-due');
    });

    // Settings
    Route::get('settings',         [SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings',         [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/test-email', [SettingsController::class, 'testEmail'])->name('settings.test-email');

    // Sale Returns
    Route::get('sale-returns/{sale}/items',  [SaleReturnController::class, 'getSaleItems'])->name('sale-returns.get-items');
    Route::resource('sale-returns', SaleReturnController::class)->only(['index', 'create', 'store', 'show']);

    // Purchase Returns
    Route::get('purchase-returns/{purchase}/items', [PurchaseReturnController::class, 'getPurchaseItems'])->name('purchase-returns.get-items');
    Route::resource('purchase-returns', PurchaseReturnController::class)->only(['index', 'create', 'store', 'show']);

    // Inventory Movement Log
    Route::get('inventory-movements', [InventoryMovementController::class, 'index'])->name('inventory-movements.index');

    // Login Tracking
    Route::get('login-logs', [LoginLogController::class, 'index'])->name('login-logs.index');

    // Backup & Restore (Admin only)
    Route::middleware('role:Admin')->group(function () {
        Route::get('backup',               [BackupController::class, 'index'])->name('backup.index');
        Route::post('backup/create',       [BackupController::class, 'create'])->name('backup.create');
        Route::get('backup/{file}',        [BackupController::class, 'download'])->name('backup.download');
        Route::delete('backup/{file}',     [BackupController::class, 'destroy'])->name('backup.destroy');
        Route::post('backup/restore',      [BackupController::class, 'restore'])->name('backup.restore');
        Route::resource('users', UserController::class);
    });
    // Categories & Units
    Route::resource('categories', CategoryController::class);
    Route::resource('units', UnitController::class);

    // AJAX (session-auth) endpoints used by create forms
    Route::get('ajax/items/search',          [ApiItemController::class, 'search'])->name('ajax.items.search');
    Route::get('ajax/items/stock-warehouse', [ApiItemController::class, 'stockByWarehouse'])->name('ajax.items.stock-warehouse');

    // Serve uploaded media from storage (avoids public/storage symlink issues)
    Route::get('media/{path}', [MediaController::class, 'show'])
        ->where('path', '.*')
        ->name('media.show');
});
