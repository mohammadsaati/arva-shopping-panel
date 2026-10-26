<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ProvinceController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\SpecificationController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductReviewController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\FooterContentController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\PanelGuideController;
use App\Http\Controllers\Admin\AdminTelegramConnectionController;
use App\Http\Controllers\Admin\PaymentGatewayController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function (): void {
    Route::middleware('guest:admin')->group(function (): void {
        Route::get('/login', [AdminAuthController::class, 'loginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])
            ->middleware('throttle:6,1')
            ->name('login.store');
    });

    Route::middleware('auth:admin')->group(function (): void {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard.index');

        Route::controller(PanelGuideController::class)
            ->prefix('help')
            ->name('help.')
            ->group(function (): void {
                Route::get('/', 'index')->name('index');
                Route::get('/icons', 'icons')->name('icons');
            });

    Route::controller(SiteSettingController::class)
        ->prefix('settings')
        ->name('settings.')
        ->group(function (): void {
            Route::get('/', 'edit')->name('edit');
            Route::put('/', 'update')->name('update');
        });

    Route::controller(AdminTelegramConnectionController::class)
        ->prefix('telegram')
        ->name('telegram.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::post('/webhook', 'registerWebhook')->name('webhook.register');
            Route::post('/link', 'createLink')->name('link');
            Route::post('/test', 'sendTest')->name('test');
            Route::delete('/', 'disconnect')->name('disconnect');
        });

    Route::controller(FooterContentController::class)
        ->prefix('footer/{kind}')
        ->name('footer.')
        ->where(['kind' => 'features|groups|links|social|apps'])
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{entryId}/edit', 'edit')->whereNumber('entryId')->name('edit');
            Route::put('/{entryId}', 'update')->whereNumber('entryId')->name('update');
            Route::delete('/{entryId}', 'destroy')->whereNumber('entryId')->name('destroy');
        });

    Route::controller(NewsletterSubscriberController::class)
        ->prefix('newsletter-subscribers')
        ->name('newsletter-subscribers.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::patch('/{subscriberId}', 'update')->whereNumber('subscriberId')->name('update');
        });

    Route::controller(UserController::class)
        ->prefix('users')
        ->name('users.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{userId}/orders', 'orders')->whereNumber('userId')->name('orders');
            Route::get('/{userId}/edit', 'edit')->whereNumber('userId')->name('edit');
            Route::put('/{userId}', 'update')->whereNumber('userId')->name('update');
            Route::get('/{userId}', 'show')->whereNumber('userId')->name('show');
        });

    Route::controller(OrderController::class)
        ->prefix('orders')
        ->name('orders.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::patch('/{orderId}/status', 'updateStatus')->whereNumber('orderId')->name('status.update');
            Route::get('/{orderId}', 'show')->whereNumber('orderId')->name('show');
        });

    Route::controller(PaymentGatewayController::class)
        ->prefix('payment-gateways')
        ->name('payment-gateways.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/{gatewayId}/edit', 'edit')->whereNumber('gatewayId')->name('edit');
            Route::put('/{gatewayId}', 'update')->whereNumber('gatewayId')->name('update');
            Route::patch('/{gatewayId}/status', 'toggle')->whereNumber('gatewayId')->name('status.toggle');
        });

    Route::controller(ProductReviewController::class)
        ->prefix('product-reviews')
        ->name('product-reviews.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/{reviewId}', 'show')->whereNumber('reviewId')->name('show');
            Route::put('/{reviewId}', 'update')->whereNumber('reviewId')->name('update');
        });

    Route::controller(DiscountController::class)
        ->prefix('discounts')
        ->name('discounts.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{discountId}/edit', 'edit')->whereNumber('discountId')->name('edit');
            Route::put('/{discountId}', 'update')->whereNumber('discountId')->name('update');
        });

    Route::controller(CityController::class)
        ->prefix('cities')
        ->name('cities.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{cityId}/edit', 'edit')->whereNumber('cityId')->name('edit');
            Route::put('/{cityId}', 'update')->whereNumber('cityId')->name('update');
            Route::get('/{cityId}', 'show')->whereNumber('cityId')->name('show');
        });

    Route::controller(ProvinceController::class)
        ->prefix('provinces')
        ->name('provinces.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{provinceId}/edit', 'edit')->whereNumber('provinceId')->name('edit');
            Route::put('/{provinceId}', 'update')->whereNumber('provinceId')->name('update');
            Route::get('/{provinceId}', 'show')->whereNumber('provinceId')->name('show');
        });

    Route::controller(CategoryController::class)
        ->prefix('categories')
        ->name('categories.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/select-options', 'selectOptions')->name('select-options');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{categoryId}/edit', 'edit')->whereNumber('categoryId')->name('edit');
            Route::put('/{categoryId}', 'update')->whereNumber('categoryId')->name('update');
            Route::get('/{categoryId}', 'show')->whereNumber('categoryId')->name('show');
        });

    Route::controller(SpecificationController::class)
        ->prefix('specifications')
        ->name('specifications.')
        ->group(function (): void {
            Route::get('/', 'catalog')->name('index');
        });

    Route::controller(SpecificationController::class)
        ->prefix('categories/{categoryId}/specifications')
        ->name('categories.specifications.')
        ->group(function (): void {
            Route::get('/', 'index')->whereNumber('categoryId')->name('index');
            Route::post('/groups', 'storeGroup')->whereNumber('categoryId')->name('groups.store');
            Route::put('/groups/{groupId}', 'updateGroup')->whereNumber('categoryId')->whereNumber('groupId')->name('groups.update');
            Route::delete('/groups/{groupId}', 'deleteGroup')->whereNumber('categoryId')->whereNumber('groupId')->name('groups.delete');
            Route::get('/fields/create', 'createField')->whereNumber('categoryId')->name('fields.create');
            Route::post('/fields', 'storeField')->whereNumber('categoryId')->name('fields.store');
            Route::get('/fields/{specificationId}/edit', 'editField')->whereNumber('categoryId')->whereNumber('specificationId')->name('fields.edit');
            Route::put('/fields/{specificationId}', 'updateField')->whereNumber('categoryId')->whereNumber('specificationId')->name('fields.update');
            Route::delete('/fields/{specificationId}', 'deleteField')->whereNumber('categoryId')->whereNumber('specificationId')->name('fields.delete');
        });

    Route::controller(BrandController::class)
        ->prefix('brands')
        ->name('brands.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{brandId}/edit', 'edit')->whereNumber('brandId')->name('edit');
            Route::put('/{brandId}', 'update')->whereNumber('brandId')->name('update');
        });

    Route::controller(ItemController::class)
        ->prefix('items')
        ->name('items.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/select-options', 'selectOptions')->name('select-options');
            Route::get('/create', 'create')->name('create');
            Route::get('/specification-fields/{categoryId}', 'specificationFields')->whereNumber('categoryId')->name('specification-fields');
            Route::post('/', 'store')->name('store');
            Route::get('/{itemId}/edit', 'edit')->whereNumber('itemId')->name('edit');
            Route::put('/{itemId}', 'update')->whereNumber('itemId')->name('update');
            Route::get('/{itemId}', 'show')->whereNumber('itemId')->name('show');
        });

    Route::controller(ProductController::class)
        ->prefix('products')
        ->name('products.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{productId}/edit', 'edit')->whereNumber('productId')->name('edit');
            Route::put('/{productId}', 'update')->whereNumber('productId')->name('update');
        });

    Route::controller(ColorController::class)
        ->prefix('colors')
        ->name('colors.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{colorId}/edit', 'edit')->whereNumber('colorId')->name('edit');
            Route::put('/{colorId}', 'update')->whereNumber('colorId')->name('update');
        });

    Route::controller(SizeController::class)
        ->prefix('sizes')
        ->name('sizes.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{sizeId}/edit', 'edit')->whereNumber('sizeId')->name('edit');
            Route::put('/{sizeId}', 'update')->whereNumber('sizeId')->name('update');
        });

    Route::controller(SliderController::class)
        ->prefix('sliders')
        ->name('sliders.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{sliderId}/edit', 'edit')->whereNumber('sliderId')->name('edit');
            Route::put('/{sliderId}', 'update')->whereNumber('sliderId')->name('update');
        });

    Route::controller(BannerController::class)
        ->prefix('banners')
        ->name('banners.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{bannerId}/edit', 'edit')->whereNumber('bannerId')->name('edit');
            Route::put('/{bannerId}', 'update')->whereNumber('bannerId')->name('update');
        });

    Route::controller(SectionController::class)
        ->prefix('sections')
        ->name('sections.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{sectionId}/edit', 'edit')->whereNumber('sectionId')->name('edit');
            Route::put('/{sectionId}', 'update')->whereNumber('sectionId')->name('update');
        });

    Route::controller(\App\Http\Controllers\Admin\Blog\BlogPostController::class)
        ->prefix('admin-blog/posts')
        ->name('admin-blog.posts.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{postId}/edit', 'edit')->whereNumber('postId')->name('edit');
            Route::put('/{postId}', 'update')->whereNumber('postId')->name('update');
            Route::delete('/{postId}', 'destroy')->whereNumber('postId')->name('destroy');
            Route::get('/{postId}', 'show')->whereNumber('postId')->name('show');
        });

    Route::controller(\App\Http\Controllers\Admin\Blog\BlogCategoryController::class)
        ->prefix('admin-blog/categories')
        ->name('admin-blog.categories.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{categoryId}/edit', 'edit')->whereNumber('categoryId')->name('edit');
            Route::put('/{categoryId}', 'update')->whereNumber('categoryId')->name('update');
            Route::delete('/{categoryId}', 'destroy')->whereNumber('categoryId')->name('destroy');
        });

    Route::controller(\App\Http\Controllers\Admin\Blog\BlogBannerController::class)
        ->prefix('admin-blog/banners')
        ->name('admin-blog.banners.')
        ->group(function (): void {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/', 'store')->name('store');
            Route::get('/{bannerId}/edit', 'edit')->whereNumber('bannerId')->name('edit');
            Route::put('/{bannerId}', 'update')->whereNumber('bannerId')->name('update');
            Route::delete('/{bannerId}', 'destroy')->whereNumber('bannerId')->name('destroy');
        });

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('auth.logout');
    });
});
