<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemController;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('items/search',          [ItemController::class, 'search']);
    Route::get('items/stock-warehouse', [ItemController::class, 'stockByWarehouse']);
});
