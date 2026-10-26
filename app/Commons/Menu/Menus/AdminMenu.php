<?php

namespace App\Commons\Menu\Menus;

use App\Commons\Menu\Contracts\MenuDefinition;
use App\Commons\Menu\MenuBuilder;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\ProductReviewServiceInterface;

class AdminMenu implements MenuDefinition
{
    public function __construct(
        private readonly OrderServiceInterface $orders,
        private readonly ProductReviewServiceInterface $reviews,
    )
    {
    }

    public function items(): array
    {
        return [

            MenuBuilder::make('داشبورد')
                ->route('admin.dashboard.index')
                ->icon('ki-home-3')
                ->routeGroup(['admin.dashboard.*'])
                ->build(),
            MenuBuilder::make('کاربران')
                ->route('admin.users.index')
                ->icon('ki-users')
                ->routeGroup(['admin.users.*'])
                ->build(),
            MenuBuilder::make('سفارش‌ها')
                ->route('admin.orders.index')
                ->icon('ki-basket')
                ->routeGroup(['admin.orders.*'])
                ->badge(fn (): int => $this->orders->pendingCount(), 'danger')
                ->build(),
            MenuBuilder::make('نظرات محصولات')
                ->route('admin.product-reviews.index')
                ->icon('ki-message-text-2')
                ->routeGroup(['admin.product-reviews.*'])
                ->badge(fn (): int => $this->reviews->pendingCount(), 'warning')
                ->build(),
            MenuBuilder::make('کدهای تخفیف')
                ->route('admin.discounts.index')
                ->icon('ki-discount')
                ->routeGroup(['admin.discounts.*'])
                ->build(),
            MenuBuilder::make('درگاه‌های پرداخت')
                ->route('admin.payment-gateways.index')
                ->icon('ki-wallet')
                ->routeGroup(['admin.payment-gateways.*'])
                ->build(),
            MenuBuilder::make('دسته‌بندی‌ها')
                ->route('admin.categories.index')
                ->icon('ki-category')
                ->routeGroup(['admin.categories.index', 'admin.categories.create', 'admin.categories.edit', 'admin.categories.show'])
                ->build(),
            MenuBuilder::make('استان‌ها و شهرها')
                ->icon('ki-geolocation')
                ->children([
                    MenuBuilder::make('استان‌ها')
                        ->route('admin.provinces.index')
                        ->routeGroup(['admin.provinces.*'])
                        ->build(),
                    MenuBuilder::make('شهرها')
                        ->route('admin.cities.index')
                        ->routeGroup(['admin.cities.*'])
                        ->build(),
                ])
                ->build(),
            MenuBuilder::make('آیتم‌ها و محصولات')
                ->icon('ki-package')
                ->children([
                    MenuBuilder::make('آیتم‌ها')
                        ->route('admin.items.index')
                        ->routeGroup(['admin.items.*'])
                        ->build(),
                    MenuBuilder::make('محصولات')
                        ->route('admin.products.index')
                        ->routeGroup(['admin.products.*'])
                        ->build(),
                    MenuBuilder::make('مشخصات کالا')
                        ->route('admin.specifications.index')
                        ->routeGroup(['admin.specifications.*', 'admin.categories.specifications.*'])
                        ->build(),
                    MenuBuilder::make('رنگ‌ها')
                        ->route('admin.colors.index')
                        ->routeGroup(['admin.colors.*'])
                        ->build(),
                    MenuBuilder::make('سایزها')
                        ->route('admin.sizes.index')
                        ->routeGroup(['admin.sizes.*'])
                        ->build(),
                    MenuBuilder::make('برندها')
                        ->route('admin.brands.index')
                        ->routeGroup(['admin.brands.*'])
                        ->build(),
                ])
                ->build(),
            MenuBuilder::make('محتوای فروشگاه')
                ->icon('ki-picture')
                ->children([
                    MenuBuilder::make('اسلایدرها')
                        ->route('admin.sliders.index')
                        ->routeGroup(['admin.sliders.*'])
                        ->build(),
                    MenuBuilder::make('بنرها')
                        ->route('admin.banners.index')
                        ->routeGroup(['admin.banners.*'])
                        ->build(),
                    MenuBuilder::make('بخش‌ها')
                        ->route('admin.sections.index')
                        ->routeGroup(['admin.sections.*'])
                        ->build(),
                ])
                ->build(),
            MenuBuilder::make('وبلاگ')
                ->icon('ki-notepad-edit')
                ->children([
                    MenuBuilder::make('نوشته‌ها')
                        ->route('admin.admin-blog.posts.index')
                        ->routeGroup(['admin.admin-blog.posts.*'])
                        ->build(),
                    MenuBuilder::make('دسته‌بندی‌های وبلاگ')
                        ->route('admin.admin-blog.categories.index')
                        ->routeGroup(['admin.admin-blog.categories.*'])
                        ->build(),
                    MenuBuilder::make('بنرهای وبلاگ')
                        ->route('admin.admin-blog.banners.index')
                        ->routeGroup(['admin.admin-blog.banners.*'])
                        ->build(),
                ])
                ->build(),
            MenuBuilder::make('تنظیمات سایت')
                ->route('admin.settings.edit')
                ->icon('ki-setting-2')
                ->routeGroup(['admin.settings.*', 'admin.footer.*', 'admin.newsletter-subscribers.*'])
                ->build(),
            MenuBuilder::make('اعلان‌های تلگرام')
                ->route('admin.telegram.index')
                ->icon('ki-message-text-2')
                ->routeGroup(['admin.telegram.*'])
                ->build(),
            MenuBuilder::make('راهنما و ابزارها')
                ->icon('ki-information-2')
                ->children([
                    MenuBuilder::make('راهنمای پنل')
                        ->route('admin.help.index')
                        ->routeGroup(['admin.help.index'])
                        ->build(),
                    MenuBuilder::make('فهرست آیکون‌های Filled')
                        ->route('admin.help.icons')
                        ->routeGroup(['admin.help.icons'])
                        ->build(),
                ])
                ->build(),
        ];
    }
}

