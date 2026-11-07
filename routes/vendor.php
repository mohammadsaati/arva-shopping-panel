<?php

use App\Http\Controllers\Vendor\DashboardController;
use App\Http\Controllers\Vendor\ItemController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\ProductController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'vendor'])->group(function (): void {
    Route::controller(DashboardController::class)->prefix('dashboard')->name('dashboard.')->group(function (): void {
        Route::get('/', 'index')->name('index');
    });
    Route::get('/', [DashboardController::class, 'index'])->name('index');

    Route::controller(ItemController::class)->prefix('items')->name('items.')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{itemId}', 'show')->whereNumber('itemId')->name('show');
    });

    Route::controller(ProductController::class)->prefix('products')->name('products.')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{productId}', 'show')->whereNumber('productId')->name('show');
    });

    Route::controller(OrderController::class)->prefix('orders')->name('orders.')->group(function (): void {
        Route::get('/', 'index')->name('index');
        Route::get('/{vendorOrderId}', 'show')->whereNumber('vendorOrderId')->name('show');
    });
});
