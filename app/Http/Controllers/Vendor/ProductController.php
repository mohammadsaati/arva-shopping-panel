<?php

namespace App\Http\Controllers\Vendor;

use App\DTO\VendorPanel\VendorProductCreateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorProductRequest;
use App\Services\Contracts\VendorPanelServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly VendorPanelServiceInterface $panel)
    {
    }

    public function index(): View
    {
        return view('vendor.products.index');
    }

    public function create(Request $request): View
    {
        return view('vendor.products.create', [
            ...$this->panel->productFormOptions($request->user()),
            'selectedItemId' => old('item_id', $request->integer('item_id') ?: null),
        ]);
    }

    public function store(StoreVendorProductRequest $request): RedirectResponse
    {
        $product = $this->panel->createProduct($request->user(), new VendorProductCreateData(
            itemId: $request->integer('item_id'),
            price: $request->integer('price'),
            stock: $request->integer('stock'),
            shippingPrice: $request->integer('shipping_price'),
            colorId: $request->filled('color_id') ? $request->integer('color_id') : null,
            sizeId: $request->filled('size_id') ? $request->integer('size_id') : null,
            images: $request->file('images', []) ?: [],
        ));

        return redirect()->route('vendor.products.show', $product->id)->with('success', 'محصول برای بررسی و تأیید ادمین ثبت شد.');
    }

    public function show(Request $request, string $productId): View
    {
        return view('vendor.products.show', ['product' => $this->panel->product($request->user(), (int) $productId)]);
    }
}
