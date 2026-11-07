<?php

namespace Tests\Feature;

use App\DTO\Cart\ShoppingCartItemAddData;
use App\DTO\CustomerAddress\CustomerAddressCreateData;
use App\DTO\Discount\DiscountCreateData;
use App\DTO\Order\PlaceOrderData;
use App\DTO\user\UserCreateData;
use App\DTO\Vendor\VendorCreateData;
use App\DTO\Commission\CommissionCreateData;
use App\DTO\Commission\CommissionUpdateData;
use App\Enums\DiscountTypeEnum;
use App\Enums\GlobalStatusEnum;
use App\Enums\MarketplaceApprovalStatusEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\UserTypeEnum;
use App\Models\City;
use App\Models\Admin;
use App\Models\CustomerAddress;
use App\Models\Discount;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorProduct;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\ShoppingCartServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use App\Services\Contracts\CommissionServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorCommissionOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_offer_snapshots_commission_after_discount_and_default_uses_cheapest_offer(): void
    {
        $city = City::factory()->create(['status' => GlobalStatusEnum::ACTIVE]);
        $commissions = app(CommissionServiceInterface::class);
        $commissionOne = $commissions->create(new CommissionCreateData('کمیسیون استاندارد', '12.50'));
        $commissionTwo = $commissions->create(new CommissionCreateData('کمیسیون ویژه', '20'));
        $users = app(UserServiceInterface::class);
        $vendorOne = $users->create(new UserCreateData(
            firstName: 'فروشنده',
            lastName: 'اول',
            phone: '09123456780',
            password: 'password123',
            type: UserTypeEnum::VENDOR,
            vendor: new VendorCreateData('فروشگاه اول', $city->id, $commissionOne->id),
        ))->vendor;
        $vendorTwo = $users->create(new UserCreateData(
            firstName: 'فروشنده',
            lastName: 'دوم',
            phone: '09123456781',
            password: 'password123',
            type: UserTypeEnum::VENDOR,
            vendor: new VendorCreateData('فروشگاه دوم', $city->id, $commissionTwo->id),
        ))->vendor;
        $product = Product::factory()->create([
            'price' => 12000,
            'stock' => 10,
            'shipping_price' => 0,
            'status' => GlobalStatusEnum::ACTIVE,
        ]);
        $product->item->update(['status' => GlobalStatusEnum::ACTIVE]);
        $product->item->category->update(['status' => GlobalStatusEnum::ACTIVE]);
        $offerOne = VendorProduct::query()->create([
            'product_id' => $product->id,
            'vendor_id' => $vendorOne->id,
            'price' => 10000,
            'stock' => 10,
            'shipping_price' => 300,
            'status' => GlobalStatusEnum::ACTIVE,
            'approval_status' => MarketplaceApprovalStatusEnum::APPROVED,
        ]);
        $offerTwo = VendorProduct::query()->create([
            'product_id' => $product->id,
            'vendor_id' => $vendorTwo->id,
            'price' => 9000,
            'stock' => 10,
            'shipping_price' => 100,
            'status' => GlobalStatusEnum::ACTIVE,
            'approval_status' => MarketplaceApprovalStatusEnum::APPROVED,
        ]);
        $buyer = User::factory()->create(['type' => UserTypeEnum::NORMAL, 'status' => GlobalStatusEnum::ACTIVE]);
        $address = CustomerAddress::createForUser($buyer, new CustomerAddressCreateData(
            provinceId: $city->province_id,
            cityId: $city->id,
            fullAddress: 'نشانی آزمایشی',
            phoneNumber: '09123456782',
            recipientName: 'خریدار',
            recipientNationalCode: null,
        ));
        $gateway = PaymentGateway::query()->firstOrFail();
        $gateway->update(['status' => GlobalStatusEnum::ACTIVE]);
        Discount::create(new DiscountCreateData(
            name: 'تخفیف آزمایشی',
            code: 'VENDORTEST',
            userId: null,
            type: DiscountTypeEnum::FIXED,
            amount: 2000,
            minOrderAmount: null,
            maxDiscountAmount: null,
            startsAt: null,
            endsAt: null,
            usageLimit: null,
            usageLimitPerUser: null,
            status: GlobalStatusEnum::ACTIVE,
        ));

        $carts = app(ShoppingCartServiceInterface::class);
        $this->get(route('shop.products.show', $product->id))->assertOk();
        $cart = $carts->add('selected-offer', new ShoppingCartItemAddData($product->id, 2, $offerOne->id));
        $this->assertSame($offerOne->id, $cart->items->first()->vendor_product_id);
        $this->assertSame(20000, $cart->subtotal());

        $order = app(OrderServiceInterface::class)->place($buyer, 'selected-offer', new PlaceOrderData(
            addressId: $address->id,
            discountCode: 'VENDORTEST',
            paymentGatewayId: $gateway->id,
        ));
        $line = $order->items->first();
        $vendorOrder = $order->vendorOrders()->firstOrFail();
        $this->assertSame(18300, $order->amount);
        $this->assertSame($offerOne->id, $line->vendor_product_id);
        $this->assertSame(1250, $line->commission_rate_bps);
        $this->assertSame(2250, $line->commission_amount);
        $this->assertSame(15750, $line->payable_amount);
        $this->assertSame(2000, $vendorOrder->discount_amount);
        $this->assertSame(18000, $vendorOrder->total_amount);
        $this->assertSame(2250, $vendorOrder->commission_amount);
        $this->assertSame(15750, $vendorOrder->payable_amount);
        $this->assertSame(8, $offerOne->fresh()->stock);

        $commissions->update($commissionOne->id, new CommissionUpdateData(percentage: '30'));
        $this->assertSame(1250, $line->fresh()->commission_rate_bps);

        $automaticCart = $carts->add('automatic-offer', new ShoppingCartItemAddData($product->id));
        $this->assertSame($offerTwo->id, $automaticCart->items->first()->vendor_product_id);
        $this->assertSame(9000, $automaticCart->subtotal());

        $directProduct = Product::factory()->create([
            'price' => 5000,
            'stock' => 3,
            'shipping_price' => 0,
            'status' => GlobalStatusEnum::ACTIVE,
        ]);
        $directProduct->item->update(['status' => GlobalStatusEnum::ACTIVE]);
        $directProduct->item->category->update(['status' => GlobalStatusEnum::ACTIVE]);
        $carts->add('direct-product', new ShoppingCartItemAddData($directProduct->id));
        $directOrder = app(OrderServiceInterface::class)->place($buyer, 'direct-product', new PlaceOrderData(
            addressId: $address->id,
            paymentGatewayId: $gateway->id,
        ));
        $this->assertSame(5000, $directOrder->amount);
        $this->assertNull($directOrder->items->first()->vendor_order_id);
        $this->assertSame(0, $directOrder->items->first()->commission_amount);

        $order->updateStatus(OrderStatusEnum::CANCELLED);
        $this->assertSame(10, $product->fresh()->stock);
        $this->assertSame(10, $offerOne->fresh()->stock);
        $this->assertSame(OrderStatusEnum::CANCELLED, $vendorOrder->fresh()->status);

        $admin = Admin::query()->create([
            'name' => 'مدیر آزمون',
            'email' => 'admin-commission@example.test',
            'password' => 'password123',
            'status' => GlobalStatusEnum::ACTIVE,
        ]);
        $this->actingAs($admin, 'admin')
            ->get(route('admin.users.create'))
            ->assertOk();
        $this->get(route('admin.commissions.index'))
            ->assertOk()
            ->assertSee('کمیسیون استاندارد');
        $this->get(route('admin.commissions.create'))->assertOk();
        $this->get(route('admin.commissions.edit', $commissionOne->id))->assertOk();
        $this->post(route('admin.commissions.store'), [
            'title' => 'کمیسیون تازه',
            'percentage' => '7.25',
        ])->assertRedirect(route('admin.commissions.index'));
        $this->assertContains('کمیسیون تازه (7.25٪)', $commissions->selectionOptions());
        $this->get(route('admin.orders.show', $order->id))
            ->assertOk()
            ->assertSee('2,250');

        $this->post(route('admin.users.store'), [
            'first_name' => 'فروشنده',
            'last_name' => 'سوم',
            'phone' => '09123456783',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'type' => UserTypeEnum::VENDOR->value,
            'vendor_company_name' => 'فروشگاه سوم',
            'vendor_city_id' => $city->id,
            'commission_id' => $commissionTwo->id,
        ])->assertRedirect(route('admin.users.index'));
        $thirdVendor = User::query()->where('phone', '09123456783')->firstOrFail()->vendor;
        $this->assertSame($commissionTwo->id, $thirdVendor->commission_id);

        $this->put(route('admin.commissions.update', $commissionTwo->id), [
            'title' => 'کمیسیون ویژه‌تر',
            'percentage' => '8.75',
        ])->assertRedirect(route('admin.commissions.index'));
        $this->assertSame(875, $thirdVendor->fresh()->commission->rate_bps);

        $this->put(route('admin.users.update', $thirdVendor->user_id), [
            'first_name' => 'فروشنده',
            'last_name' => 'سوم',
            'phone' => '09123456783',
            'type' => UserTypeEnum::VENDOR->value,
            'status' => GlobalStatusEnum::ACTIVE->value,
            'vendor_company_name' => 'فروشگاه سوم',
            'vendor_city_id' => $city->id,
            'commission_id' => '',
        ])->assertRedirect(route('admin.users.show', $thirdVendor->user_id));
        $this->assertNull($thirdVendor->fresh()->commission_id);
        VendorProduct::query()->create([
            'product_id' => $directProduct->id,
            'vendor_id' => $thirdVendor->id,
            'price' => 4500,
            'stock' => 2,
            'shipping_price' => 0,
            'status' => GlobalStatusEnum::ACTIVE,
            'approval_status' => MarketplaceApprovalStatusEnum::APPROVED,
        ]);
        $carts->add('vendor-without-commission', new ShoppingCartItemAddData($directProduct->id));
        $zeroCommissionOrder = app(OrderServiceInterface::class)->place($buyer, 'vendor-without-commission', new PlaceOrderData(
            addressId: $address->id,
            paymentGatewayId: $gateway->id,
        ));
        $this->assertSame(0, $zeroCommissionOrder->items->first()->commission_amount);
        $this->assertSame(4500, $zeroCommissionOrder->vendorOrders()->firstOrFail()->payable_amount);

        $this->post(route('admin.users.store'), [
            'first_name' => 'فروشنده',
            'last_name' => 'بدون کمیسیون',
            'phone' => '09123456784',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'type' => UserTypeEnum::VENDOR->value,
            'vendor_company_name' => 'فروشگاه بدون کمیسیون',
            'vendor_city_id' => $city->id,
        ])->assertRedirect(route('admin.users.index'));
        $this->assertNull(User::query()->where('phone', '09123456784')->firstOrFail()->vendor->commission_id);
    }
}
