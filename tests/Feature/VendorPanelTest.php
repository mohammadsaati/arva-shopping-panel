<?php

namespace Tests\Feature;

use App\DTO\Cart\ShoppingCartItemAddData;
use App\DTO\CustomerAddress\CustomerAddressCreateData;
use App\DTO\Order\PlaceOrderData;
use App\DTO\user\UserCreateData;
use App\DTO\Vendor\VendorCreateData;
use App\Enums\GlobalStatusEnum;
use App\Enums\UserTypeEnum;
use App\Models\Category;
use App\Models\City;
use App\Models\CustomerAddress;
use App\Models\Item;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\User;
use App\Models\VendorOrder;
use App\Models\VendorProduct;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\ShoppingCartServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_vendor_can_create_and_see_only_own_catalog_and_orders(): void
    {
        $city = City::factory()->create(['status' => GlobalStatusEnum::ACTIVE]);
        $category = Category::factory()->create(['status' => GlobalStatusEnum::ACTIVE]);
        $users = app(UserServiceInterface::class);
        $first = $users->create(new UserCreateData(
            firstName: 'فروشنده', lastName: 'اول', phone: '09120000001', password: 'password123',
            type: UserTypeEnum::VENDOR, vendor: new VendorCreateData('فروشگاه اول', $city->id, null),
        ));
        $second = $users->create(new UserCreateData(
            firstName: 'فروشنده', lastName: 'دوم', phone: '09120000002', password: 'password123',
            type: UserTypeEnum::VENDOR, vendor: new VendorCreateData('فروشگاه دوم', $city->id, null),
        ));
        $buyer = User::factory()->create(['type' => UserTypeEnum::NORMAL, 'status' => GlobalStatusEnum::ACTIVE]);

        $this->get(route('vendor.index'))->assertRedirect(route('login'));
        $this->post(route('login.store'), [
            'phone' => $first->phone,
            'password' => 'password123',
        ])->assertRedirect(route('vendor.index'));
        $this->get(route('vendor.dashboard.index'))->assertOk();
        $this->post(route('logout'))->assertRedirect(route('home'));
        $this->actingAs($buyer)->get(route('vendor.index'))->assertForbidden();
        $this->actingAs($first)->get(route('vendor.index'))->assertOk();
        $this->get(route('vendor.items.create'))->assertOk();
        $this->get(route('vendor.products.create'))->assertOk();

        $this->post(route('vendor.items.store'), [
            'name' => 'آیتم فروشنده اول', 'slug' => 'vendor-one-item', 'code' => 981001,
            'category_id' => $category->id,
        ])->assertRedirect();
        $firstItem = Item::query()->where('slug', 'vendor-one-item')->firstOrFail();
        $this->assertSame($first->vendor->id, $firstItem->vendor_id);

        $this->actingAs($second)->post(route('vendor.items.store'), [
            'name' => 'آیتم فروشنده دوم', 'slug' => 'vendor-two-item', 'code' => 981002,
            'category_id' => $category->id,
        ])->assertRedirect();
        $secondItem = Item::query()->where('slug', 'vendor-two-item')->firstOrFail();
        $this->get(route('vendor.items.show', $firstItem->id))->assertNotFound();
        $this->get(route('vendor.items.index'))->assertOk()->assertSee('آیتم فروشنده دوم')->assertDontSee('آیتم فروشنده اول');
        $this->post(route('vendor.products.store'), [
            'item_id' => $firstItem->id, 'price' => 10000, 'stock' => 3, 'shipping_price' => 0,
        ])->assertSessionHasErrors('item_id');

        $this->actingAs($first)->post(route('vendor.products.store'), [
            'item_id' => $firstItem->id, 'price' => 10000, 'stock' => 3, 'shipping_price' => 0,
        ])->assertRedirect();
        $product = Product::query()->where('item_id', $firstItem->id)->firstOrFail();
        $offer = VendorProduct::query()->where('product_id', $product->id)->firstOrFail();
        $this->assertSame($first->vendor->id, $offer->vendor_id);
        $this->actingAs($second)->get(route('vendor.products.show', $product->id))->assertNotFound();
        $this->get(route('vendor.products.index'))->assertOk()->assertDontSee('آیتم فروشنده اول');

        $address = CustomerAddress::createForUser($buyer, new CustomerAddressCreateData(
            provinceId: $city->province_id, cityId: $city->id, fullAddress: 'نشانی خریدار',
            phoneNumber: '09120000003', recipientName: 'خریدار', recipientNationalCode: null,
        ));
        $gateway = PaymentGateway::query()->firstOrFail();
        $gateway->update(['status' => GlobalStatusEnum::ACTIVE]);
        app(ShoppingCartServiceInterface::class)->add('vendor-panel-cart', new ShoppingCartItemAddData($product->id));
        app(OrderServiceInterface::class)->place($buyer, 'vendor-panel-cart', new PlaceOrderData(
            addressId: $address->id, paymentGatewayId: $gateway->id,
        ));
        $vendorOrder = VendorOrder::query()->where('vendor_id', $first->vendor->id)->firstOrFail();

        $this->get(route('vendor.orders.show', $vendorOrder->id))->assertNotFound();
        $this->actingAs($first)->get(route('vendor.orders.show', $vendorOrder->id))->assertOk();
        $this->get(route('vendor.orders.index'))->assertOk()->assertSee('#' . $vendorOrder->order_id);
    }
}
