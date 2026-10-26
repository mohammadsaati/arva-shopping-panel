<?php

namespace App\Services;

use App\DTO\Order\PlaceOrderData;
use App\DTO\Discount\DiscountQuoteRequestData;
use App\DTO\Order\OrderFilterData;
use App\DTO\Order\OrderHistoryFilterData;
use App\DTO\Order\OrderStatusUpdateData;
use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\CustomerAddress;
use App\Models\OrderItem;
use App\Models\PaymentGateway;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\ShoppingCart;
use App\Models\ShoppingCartItem;
use App\Models\User;
use App\Services\Contracts\OrderServiceInterface;
use App\Services\Contracts\DiscountServiceInterface;
use App\Services\Contracts\ShoppingCartServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Illuminate\Contracts\Pagination\Paginator;

class OrderService implements OrderServiceInterface
{
    private const string PENDING_ORDERS_COUNT = 'pending_orders_count';

    public function __construct(
        private readonly ShoppingCartServiceInterface $shoppingCart,
        private readonly DiscountServiceInterface $discounts,
    ) {}

    public function place(User $user, string $shoppingKey, PlaceOrderData $data): Order
    {
        return Order::runInTransaction(function () use ($user, $shoppingKey, $data): Order {
            $cart = ShoppingCart::lockForShoppingKey($shoppingKey);

            if ($cart === null) {
                throw ValidationException::withMessages(['cart' => 'سبد خرید شما خالی است.']);
            }

            $cartItems = ShoppingCartItem::lockForCart($cart->id);

            if ($cartItems->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'سبد خرید شما خالی است.']);
            }

            $address = CustomerAddress::findForCheckout($user, $data->addressId);

            if ($address === null) {
                throw ValidationException::withMessages([
                    'address_id' => 'نشانی انتخاب‌شده معتبر نیست. لطفاً یکی از نشانی‌های خود را انتخاب کنید.',
                ]);
            }

            $subtotal = 0;
            $products = [];

            foreach ($cartItems as $cartItem) {
                $product = Product::findPurchasableForCart($cartItem->product_id, lock: true);

                if ($product === null || $cartItem->count > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => 'یکی از کالاها دیگر موجود نیست یا تعداد آن از موجودی بیشتر است.',
                    ]);
                }

                $lineTotal = $product->price * $cartItem->count;
                $subtotal += $lineTotal;
                $products[] = $product;
                $cartItem->setRelation('product', $product);
            }

            $cart->setRelation('items', $cartItems);
            $discountQuote = $data->discountCode !== null
                ? $this->discounts->quoteForCart(
                    new DiscountQuoteRequestData(code: $data->discountCode),
                    $cart,
                    $user,
                    lock: true,
                )
                : null;

            $shippingTotal = $this->shoppingCart->shippingForProducts($products, $address->city_id);
            $payable = max(0, $subtotal - ($discountQuote?->discountAmount ?? 0)) + $shippingTotal;
            $gateway = null;

            if ($payable > 0) {
                if ($data->paymentGatewayId === null) {
                    throw ValidationException::withMessages([
                        'payment_gateway_id' => 'یک روش پرداخت انتخاب کنید.',
                    ]);
                }

                $gateway = PaymentGateway::findActive($data->paymentGatewayId);

                if ($gateway === null) {
                    throw ValidationException::withMessages([
                        'payment_gateway_id' => 'روش پرداخت انتخاب‌شده فعال نیست.',
                    ]);
                }
            }

            $order = Order::createForCheckout($user, $address);

            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                OrderItem::createForOrder($order, $product, $cartItem->count, $product->price * $cartItem->count);
                $product->decreaseStock($cartItem->count);
            }

            $order->updateTotals(
                subtotal: $subtotal,
                shippingAmount: $shippingTotal,
                discountAmount: $discountQuote?->discountAmount ?? 0,
                discount: $discountQuote?->discount,
            );

            if ($order->amount < 1) {
                $order->markPaid();
            } elseif ($gateway !== null) {
                Transaction::createForOrderPayment($order, $user, $gateway);
            }

            ShoppingCartItem::clearCart($cart->id);

            return Order::detailsForUser($user->id, $order->id);
        });
    }

    public function details(User $user, int $orderId): Order
    {
        return Order::detailsForUser($user->id, $orderId);
    }

    public function filterForAdmin(OrderFilterData $filters): Paginator
    {
        return Order::filterForAdmin($filters);
    }

    public function history(User $user, OrderHistoryFilterData $filters): Paginator
    {
        return Order::historyForUser($user->id, $filters);
    }

    public function detailsForAdmin(int $orderId): Order
    {
        return Order::detailsForAdmin($orderId);
    }

    public function updateStatus(int $orderId, OrderStatusUpdateData $data): Order
    {
        return Order::runInTransaction(function () use ($orderId, $data): Order {
            $order = Order::lockForStatusUpdate($orderId);

            if ($data->status !== null) {
                $order->updateStatus($data->status);
            }

            Cache::forget(self::PENDING_ORDERS_COUNT);

            return Order::detailsForAdmin($orderId);
        });
    }

    public function pendingCount(): int
    {
        return Cache::remember(
            self::PENDING_ORDERS_COUNT,
            30,
            fn () => Order::countByStatus(OrderStatusEnum::PENDING)
        );
    }

    public function expireStaleUnpaid(int $olderThanHours): array
    {
        $expiredIds = Order::expireStaleUnpaid($olderThanHours);

        if ($expiredIds !== []) {
            Cache::forget(self::PENDING_ORDERS_COUNT);
        }

        return $expiredIds;
    }
}
