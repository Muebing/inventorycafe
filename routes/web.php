<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockInController;
use App\Http\Controllers\StockOutController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('/categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/suppliers')->name('suppliers.')->group(function () {
        Route::get('/', [SupplierController::class, 'index'])->name('index');
        Route::post('/', [SupplierController::class, 'store'])->name('store');
        Route::put('/{supplier}', [SupplierController::class, 'update'])->name('update');
        Route::delete('/{supplier}', [SupplierController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/items')->name('items.')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('index');
        Route::post('/', [ItemController::class, 'store'])->name('store');
        Route::put('/{item}', [ItemController::class, 'update'])->name('update');
        Route::delete('/{item}', [ItemController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/stock-in')->name('stock-in.')->group(function () {
        Route::get('/', [StockInController::class, 'index'])->name('index');
        Route::post('/', [StockInController::class, 'store'])->name('store');
        Route::delete('/{stockIn}', [StockInController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/stock-out')->name('stock-out.')->group(function () {
        Route::get('/', [StockOutController::class, 'index'])->name('index');
        Route::post('/', [StockOutController::class, 'store'])->name('store');
        Route::delete('/{stockOut}', [StockOutController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('/stock-adjustments')->name('stock-adjustments.')->group(function () {
        Route::get('/', [StockAdjustmentController::class, 'index'])->name('index');
        Route::post('/', [StockAdjustmentController::class, 'store'])->name('store');
        Route::delete('/{stockAdjustment}', [StockAdjustmentController::class, 'destroy'])->name('destroy');
    });

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
});

Route::redirect('/', '/dashboard');
