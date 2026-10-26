<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountAddressController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\ShoppingCartController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/suggest', [ShopController::class, 'suggest'])
    ->middleware('throttle:30,1')
    ->name('shop.suggest');
Route::get('/shop/products/{productId}', [ShopController::class, 'show'])->whereNumber('productId')->name('shop.products.show');
Route::post('/items/{itemId}/reviews', [ProductReviewController::class, 'store'])
    ->whereNumber('itemId')
    ->middleware(['auth', 'throttle:5,1'])
    ->name('product-reviews.store');
Route::get('/categories/{slug}', [ShopController::class, 'category'])->name('categories.show');
Route::get('/sections/{slug}', [ShopController::class, 'section'])->name('sections.show');
Route::post('/newsletter', [NewsletterController::class, 'store'])->middleware('throttle:5,1')->name('newsletter.store');

Route::get('/cart', [ShoppingCartController::class, 'index'])->name('cart.index');
Route::post('/cart/discount', [ShoppingCartController::class, 'applyDiscount'])
    ->middleware('throttle:20,1')
    ->name('cart.discount.apply');
Route::delete('/cart/discount', [ShoppingCartController::class, 'removeDiscount'])->name('cart.discount.remove');
Route::post('/cart/update', [ShoppingCartController::class, 'update'])->name('cart.update');
Route::post('/cart/items/remove', [ShoppingCartController::class, 'remove'])->name('cart.items.remove');
Route::post('/cart/items/{productId}', [ShoppingCartController::class, 'store'])
    ->whereNumber('productId')
    ->middleware('throttle:20,1')
    ->name('cart.items.store');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [UserAuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [UserAuthController::class, 'login'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/register', [UserAuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [UserAuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');
    Route::get('/forgot-password', [UserAuthController::class, 'forgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [UserAuthController::class, 'sendPasswordResetLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');
    Route::get('/reset-password/{token}', [UserAuthController::class, 'resetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [UserAuthController::class, 'resetPassword'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/payments/zarinpal/callback', [PaymentController::class, 'zarinpalCallback'])
    ->middleware('throttle:30,1')
    ->name('payments.zarinpal.callback');

Route::get('/payments/result', [PaymentController::class, 'result'])
    ->name('payments.result');

Route::middleware('auth')->group(function (): void {
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/checkout', [CheckoutController::class, 'store'])->middleware('throttle:10,1')->name('checkout.store');
    Route::get('/orders/{orderId}', [OrderController::class, 'show'])->whereNumber('orderId')->name('orders.show');
    Route::post('/orders/{orderId}/pay', [PaymentController::class, 'start'])
        ->whereNumber('orderId')
        ->middleware('throttle:10,1')
        ->name('orders.pay');
});

Route::middleware('auth')->prefix('account')->name('account.')->group(function (): void {
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/', [AccountController::class, 'show'])->name('show');
    Route::patch('/', [AccountController::class, 'update'])->name('update');
    Route::put('/password', [AccountController::class, 'changePassword'])->name('password.update');

    Route::controller(AccountAddressController::class)
        ->prefix('addresses')
        ->name('addresses.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{addressId}/edit', 'edit')->whereNumber('addressId')->name('edit');
            Route::put('/{addressId}', 'update')->whereNumber('addressId')->name('update');
        });
});

