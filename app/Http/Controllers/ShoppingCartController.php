<?php

namespace App\Http\Controllers;

use App\DTO\Cart\ShoppingCartItemAddData;
use App\DTO\Cart\ShoppingCartUpdateData;
use App\DTO\Discount\DiscountQuoteRequestData;
use App\Http\Requests\RemoveShoppingCartItemRequest;
use App\Http\Requests\ApplyDiscountRequest;
use App\Http\Requests\StoreCartItemRequest;
use App\Http\Requests\UpdateShoppingCartRequest;
use App\Services\Contracts\ShoppingCartServiceInterface;
use App\Services\Contracts\DiscountServiceInterface;
use App\Models\User;
use App\Support\ShoppingCartSession;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ShoppingCartController extends Controller
{
    public function __construct(
        private readonly ShoppingCartServiceInterface $shoppingCart,
        private readonly DiscountServiceInterface $discounts,
    ) {}

    public function index(Request $request): View
    {
        $cart = $this->shoppingCart->get(ShoppingCartSession::key($request));
        $discountQuote = null;
        $discountCode = $request->session()->get('discount_code');

        if ($cart === null || $cart->items->isEmpty()) {
            $request->session()->forget('discount_code');
        } elseif (is_string($discountCode) && $discountCode !== '') {
            try {
                $user = $request->user();
                $discountQuote = $this->discounts->quoteForCart(
                    new DiscountQuoteRequestData(code: $discountCode),
                    $cart,
                    $user instanceof User ? $user : null,
                );
            } catch (ValidationException $exception) {
                $request->session()->forget('discount_code');
                $request->session()->flash(
                    'discount_error',
                    $exception->errors()['discount_code'][0] ?? 'کد تخفیف دیگر برای این سبد خرید معتبر نیست.',
                );
            }
        }

        return view('cart.index', [
            'cart' => $cart,
            'discountQuote' => $discountQuote,
        ]);
    }

    public function applyDiscount(ApplyDiscountRequest $request): RedirectResponse
    {
        $cart = $this->shoppingCart->get(ShoppingCartSession::key($request));

        if ($cart === null || $cart->items->isEmpty()) {
            throw ValidationException::withMessages(['discount_code' => 'برای استفاده از کد تخفیف ابتدا کالایی به سبد خرید اضافه کنید.']);
        }

        $user = $request->user();
        $quote = $this->discounts->quoteForCart(
            new DiscountQuoteRequestData(code: $request->code()),
            $cart,
            $user instanceof User ? $user : null,
        );

        $request->session()->put('discount_code', $quote->discount->code);
        $request->session()->forget('discount_error');

        return redirect()->route('cart.index')->with('success', 'کد تخفیف با موفقیت اعمال شد.');
    }

    public function removeDiscount(Request $request): RedirectResponse
    {
        $request->session()->forget('discount_code');
        $request->session()->forget('discount_error');

        return redirect()->route('cart.index')->with('success', 'کد تخفیف از سبد خرید حذف شد.');
    }

    public function store(StoreCartItemRequest $request, int $productId): RedirectResponse
    {
        $this->shoppingCart->add(
            ShoppingCartSession::key($request),
            new ShoppingCartItemAddData(
                productId: $productId,
                quantity: $request->quantity(),
            ),
        );

        return redirect()->route('cart.index')->with('success', 'محصول به سبد خرید اضافه شد.');
    }

    public function update(UpdateShoppingCartRequest $request): RedirectResponse
    {
        $this->shoppingCart->update(
            ShoppingCartSession::key($request),
            new ShoppingCartUpdateData(quantities: $request->quantities()),
        );

        return redirect()->route('cart.index')->with('success', 'سبد خرید به‌روزرسانی شد.');
    }

    public function remove(RemoveShoppingCartItemRequest $request): RedirectResponse
    {
        $this->shoppingCart->remove(
            ShoppingCartSession::key($request),
            $request->productId(),
        );

        return redirect()->route('cart.index')->with('success', 'محصول از سبد خرید حذف شد.');
    }
}
