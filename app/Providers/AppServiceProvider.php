<?php

namespace App\Providers;

use App\Commons\Menu\MenuRegistry;
use App\Commons\Menu\Menus\AdminMenu;
use App\Services\CategoryService;
use App\Services\AdminAuthService;
use App\Services\CityService;
use App\Services\ProvinceService;
use App\Services\BrandService;
use App\Services\BannerService;
use App\Services\BlogManagementService;
use App\Services\BlogStorefrontService;
use App\Services\Contracts\BrandServiceInterface;
use App\Services\Contracts\BannerServiceInterface;
use App\Services\Contracts\BlogManagementServiceInterface;
use App\Services\Contracts\BlogStorefrontServiceInterface;
use App\Services\Contracts\CategoryServiceInterface;
use App\Services\Contracts\AdminAuthServiceInterface;
use App\Services\Contracts\CityServiceInterface;
use App\Services\Contracts\ProvinceServiceInterface;
use App\Services\Contracts\ColorServiceInterface;
use App\Services\Contracts\ItemServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\Contracts\ProductReviewServiceInterface;
use App\Services\Contracts\SizeServiceInterface;
use App\Services\Contracts\SectionServiceInterface;
use App\Services\Contracts\SliderServiceInterface;
use App\Services\Contracts\StorefrontServiceInterface;
use App\Services\Contracts\SpecificationServiceInterface;
use App\Services\Contracts\UserAuthServiceInterface;
use App\Services\Contracts\UserPasswordResetServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\CustomerAddressServiceInterface;
use App\Services\Contracts\ShoppingCartServiceInterface;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\DashboardServiceInterface;
use App\Services\Contracts\DiscountServiceInterface;
use App\Services\ItemService;
use App\Services\ProductService;
use App\Services\ProductReviewService;
use App\Services\ColorService;
use App\Services\SizeService;
use App\Services\SectionService;
use App\Services\SliderService;
use App\Services\StorefrontService;
use App\Services\SpecificationService;
use App\Services\UserAuthService;
use App\Services\UserPasswordResetService;
use App\Services\UserService;
use App\Services\CustomerAddressService;
use App\Services\ShoppingCartService;
use App\Services\OrderService;
use App\Services\DashboardService;
use App\Services\DiscountService;
use App\Services\SiteSettingService;
use App\Services\Contracts\SiteSettingServiceInterface;
use App\Services\FooterContentService;
use App\Services\Contracts\FooterContentServiceInterface;
use App\Services\NewsletterService;
use App\Services\Contracts\NewsletterServiceInterface;
use App\Services\PaymentGatewayService;
use App\Services\PaymentService;
use App\Services\ZarinpalClient;
use App\Services\Contracts\PaymentGatewayServiceInterface;
use App\Services\Contracts\PaymentServiceInterface;
use App\Services\Contracts\ZarinpalClientInterface;
use App\Services\Contracts\AdminTelegramConnectionServiceInterface;
use App\Services\Contracts\TelegramBotClientInterface;
use App\Services\AdminTelegramConnectionService;
use App\Services\TelegramBotClient;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerServices();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        app()->setLocale(config('app.locale'));

        if (class_exists(\Laravel\Horizon\Horizon::class)) {
            \Laravel\Horizon\Horizon::auth(static fn ($request): bool => $request->user('admin') !== null);
        }

        MenuRegistry::register('admin', new AdminMenu(
            app(OrderServiceInterface::class),
            app(ProductReviewServiceInterface::class),
        ));

        View::composer('components.user.header', function ($view): void {
            $shoppingKey = request()->session()->get('shopping_key');

            $view->with([
                'navigationCategories' => app(StorefrontServiceInterface::class)->navigationCategories(),
                'cartCount' => is_string($shoppingKey)
                    ? app(ShoppingCartServiceInterface::class)->count($shoppingKey)
                    : 0,
            ]);
        });

        View::composer('components.user.footer', function ($view): void {
            $view->with(app(SiteSettingServiceInterface::class)->footer());
        });
    }

    private function registerServices(): void
    {
        $this->app->singleton(AdminAuthServiceInterface::class, AdminAuthService::class);
        $this->app->singleton(CategoryServiceInterface::class, CategoryService::class);
        $this->app->singleton(CityServiceInterface::class, CityService::class);
        $this->app->singleton(ProvinceServiceInterface::class, ProvinceService::class);
        $this->app->singleton(BrandServiceInterface::class, BrandService::class);
        $this->app->singleton(BannerServiceInterface::class, BannerService::class);
        $this->app->singleton(BlogManagementServiceInterface::class, BlogManagementService::class);
        $this->app->singleton(BlogStorefrontServiceInterface::class, BlogStorefrontService::class);
        $this->app->singleton(ColorServiceInterface::class, ColorService::class);
        $this->app->singleton(ItemServiceInterface::class, ItemService::class);
        $this->app->singleton(ProductServiceInterface::class, ProductService::class);
        $this->app->singleton(ProductReviewServiceInterface::class, ProductReviewService::class);
        $this->app->singleton(SizeServiceInterface::class, SizeService::class);
        $this->app->singleton(SectionServiceInterface::class, SectionService::class);
        $this->app->singleton(SliderServiceInterface::class, SliderService::class);
        $this->app->singleton(StorefrontServiceInterface::class, StorefrontService::class);
        $this->app->singleton(SpecificationServiceInterface::class, SpecificationService::class);
        $this->app->singleton(UserAuthServiceInterface::class, UserAuthService::class);
        $this->app->singleton(UserPasswordResetServiceInterface::class, UserPasswordResetService::class);
        $this->app->singleton(ShoppingCartServiceInterface::class, ShoppingCartService::class);
        $this->app->singleton(OrderServiceInterface::class, OrderService::class);
        $this->app->singleton(DashboardServiceInterface::class, DashboardService::class);
        $this->app->singleton(DiscountServiceInterface::class, DiscountService::class);
        $this->app->singleton(SiteSettingServiceInterface::class, SiteSettingService::class);
        $this->app->singleton(FooterContentServiceInterface::class, FooterContentService::class);
        $this->app->singleton(NewsletterServiceInterface::class, NewsletterService::class);
        $this->app->singleton(PaymentGatewayServiceInterface::class, PaymentGatewayService::class);
        $this->app->singleton(ZarinpalClientInterface::class, ZarinpalClient::class);
        $this->app->singleton(PaymentServiceInterface::class, PaymentService::class);
        $this->app->singleton(TelegramBotClientInterface::class, TelegramBotClient::class);
        $this->app->singleton(AdminTelegramConnectionServiceInterface::class, AdminTelegramConnectionService::class);
        $this->app->singleton(UserServiceInterface::class, UserService::class);
        $this->app->singleton(CustomerAddressServiceInterface::class, CustomerAddressService::class);
    }
}
