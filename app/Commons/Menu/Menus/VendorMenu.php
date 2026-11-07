<?php

namespace App\Commons\Menu\Menus;

use App\Commons\Menu\Contracts\MenuDefinition;
use App\Commons\Menu\MenuBuilder;

class VendorMenu implements MenuDefinition
{
    public function items(): array
    {
        return [
            MenuBuilder::make('داشبورد')->route('vendor.dashboard.index')->icon('ki-home-3')
                ->routeGroup('vendor.dashboard.*')->build(),
            MenuBuilder::make('آیتم‌های من')->route('vendor.items.index')->icon('ki-category')
                ->routeGroup('vendor.items.*')->build(),
            MenuBuilder::make('محصولات من')->route('vendor.products.index')->icon('ki-package')
                ->routeGroup('vendor.products.*')->build(),
            MenuBuilder::make('سفارش‌های من')->route('vendor.orders.index')->icon('ki-basket')
                ->routeGroup('vendor.orders.*')->build(),
        ];
    }
}
