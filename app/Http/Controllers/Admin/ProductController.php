<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Product\ProductCreateData;
use App\DTO\Product\ProductCityRateData;
use App\DTO\Product\ProductUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminProductRequest;
use App\Http\Requests\UpdateAdminProductRequest;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private readonly ProductServiceInterface $products)
    {
    }

    public function index(): View
    {
        return view('admin.products.index');
    }

    public function create(Request $request): View
    {
        $selectedItemId = old('item_id', $request->integer('item_id') ?: null);
        $selectedItemId = ctype_digit((string) $selectedItemId) && (int) $selectedItemId > 0
            ? (int) $selectedItemId
            : null;

        return view('admin.products.create', [
            ...$this->products->formOptions($selectedItemId),
            'selectedItemId' => $selectedItemId,
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
        ]);
    }

    public function store(StoreAdminProductRequest $request): RedirectResponse
    {
        $product = $this->products->create(new ProductCreateData(
            itemId: $request->integer('item_id'),
            price: $request->integer('price'),
            colorId: $request->filled('color_id') ? $request->integer('color_id') : null,
            sizeId: $request->filled('size_id') ? $request->integer('size_id') : null,
            stock: $request->integer('stock'),
            shippingPrice: $request->integer('shipping_price'),
            status: $request->enum('status', GlobalStatusEnum::class),
            images: $request->file('images', []) ?: [],
            cityRates: array_map(
                static fn (array $rate): ProductCityRateData => new ProductCityRateData(
                    cityId: (int) $rate['city_id'],
                    shippingPrice: (int) $rate['shipping_price'],
                ),
                $request->input('city_rates') ?: [],
            ),
        ));

        return redirect()
            ->route('admin.products.edit', $product->id)
            ->with('success', 'محصول جدید با موفقیت به آیتم اضافه شد.');
    }

    public function edit(string $productId): View
    {
        $product = $this->products->details((int) $productId);
        $selectedItemId = old('item_id', $product->item_id);
        $selectedItemId = ctype_digit((string) $selectedItemId) && (int) $selectedItemId > 0
            ? (int) $selectedItemId
            : $product->item_id;

        return view('admin.products.edit', [
            ...$this->products->formOptions($selectedItemId),
            'product' => $product,
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
        ]);
    }

    public function update(UpdateAdminProductRequest $request, string $productId): RedirectResponse
    {
        $product = $this->products->update((int) $productId, new ProductUpdateData(
            itemId: $request->integer('item_id'),
            price: $request->integer('price'),
            colorId: $request->filled('color_id') ? $request->integer('color_id') : null,
            colorIdProvided: $request->has('color_id'),
            sizeId: $request->filled('size_id') ? $request->integer('size_id') : null,
            sizeIdProvided: $request->has('size_id'),
            stock: $request->integer('stock'),
            shippingPrice: $request->integer('shipping_price'),
            status: $request->enum('status', GlobalStatusEnum::class),
            images: $request->file('images', []) ?: [],
            replacements: $request->file('replacements', []) ?: [],
            deleteImageIds: array_map('intval', $request->input('delete_image_ids', [])),
            cityRates: array_map(
                static fn (array $rate): ProductCityRateData => new ProductCityRateData(
                    cityId: (int) $rate['city_id'],
                    shippingPrice: (int) $rate['shipping_price'],
                ),
                $request->input('city_rates') ?: [],
            ),
            cityRatesProvided: $request->boolean('city_rates_present'),
        ));

        return redirect()
            ->route('admin.products.edit', $product->id)
            ->with('success', 'محصول با موفقیت ویرایش شد.');
    }
}
