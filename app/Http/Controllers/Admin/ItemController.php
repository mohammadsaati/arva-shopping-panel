<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Item\ItemCreateData;
use App\DTO\Item\ItemProductCreateData;
use App\DTO\Item\ItemSelectOptionsData;
use App\DTO\Item\ItemUpdateData;
use App\DTO\Product\ProductCityRateData;
use App\Enums\GlobalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminItemRequest;
use App\Http\Requests\ItemSelectOptionsRequest;
use App\Http\Requests\UpdateAdminItemRequest;
use App\Services\Contracts\ItemServiceInterface;
use Hekmatinasser\Verta\Verta;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{
    public function __construct(private readonly ItemServiceInterface $items)
    {
    }

    public function index(): View
    {
        return view('admin.items.index');
    }

    public function selectOptions(ItemSelectOptionsRequest $request): JsonResponse
    {
        return response()->json(
            $this->items->selectOptions(new ItemSelectOptionsData(
                search: $request->string('search')->value(),
                selectedIds: array_map('intval', $request->array('selected_ids')),
                page: $request->integer('page', 1),
            )),
        );
    }

    public function create(): View
    {
        $categoryId = old('category_id');

        return view('admin.items.create', [
            ...$this->items->formOptions(),
            'specificationGroups' => ctype_digit((string) $categoryId)
                ? $this->items->specificationGroups((int) $categoryId)
                : collect(),
            'selectedCategoryId' => ctype_digit((string) $categoryId) ? (int) $categoryId : null,
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
        ]);
    }

    public function store(StoreAdminItemRequest $request): RedirectResponse
    {
        $item = $this->items->create(new ItemCreateData(
            name: $request->string('name')->value(),
            slug: $request->string('slug')->value(),
            code: $request->integer('code'),
            categoryId: $request->integer('category_id'),
            brandId: $request->filled('brand_id') ? $request->integer('brand_id') : null,
            image: $request->file('image'),
            description: $request->filled('description') ? $request->string('description')->value() : null,
            status: $request->enum('status', GlobalStatusEnum::class),
            isAmazing: $request->boolean('is_amazing'),
            amazingEndTime: $request->filled('amazing_end_time')
                ? Verta::parseFormat('Y/m/d H:i:s', $request->string('amazing_end_time')->value())->toCarbon()
                : null,
            product: $request->filled('product.price')
                ? new ItemProductCreateData(
                    price: $request->integer('product.price'),
                    colorId: $request->filled('product.color_id') ? $request->integer('product.color_id') : null,
                    sizeId: $request->filled('product.size_id') ? $request->integer('product.size_id') : null,
                    stock: $request->integer('product.stock'),
                    shippingPrice: $request->integer('product.shipping_price'),
                    status: $request->filled('product.status')
                        ? $request->enum('product.status', GlobalStatusEnum::class)
                        : GlobalStatusEnum::ACTIVE,
                    cityRates: array_map(
                        static fn (array $rate): ProductCityRateData => new ProductCityRateData(
                            cityId: (int) $rate['city_id'],
                            shippingPrice: (int) $rate['shipping_price'],
                        ),
                        $request->input('city_rates') ?: [],
                    ),
                )
                : null,
            specifications: $request->specificationAnswers(),
        ));

        return redirect()
            ->route('admin.items.show', $item->id)
            ->with('success', $request->filled('product.price')
                ? 'آیتم و محصول اولیه با موفقیت ایجاد شدند.'
                : 'آیتم جدید با موفقیت ایجاد شد.');
    }

    public function show(string $itemId): View
    {
        return view('admin.items.show', [
            'item' => $this->items->details((int) $itemId),
        ]);
    }

    public function edit(string $itemId): View
    {
        $item = $this->items->details((int) $itemId);
        $categoryId = old('category_id', $item->category_id);

        return view('admin.items.edit', [
            ...$this->items->formOptions($item),
            'item' => $item,
            'specificationGroups' => ctype_digit((string) $categoryId)
                ? $this->items->specificationGroups((int) $categoryId)
                : collect(),
            'selectedCategoryId' => ctype_digit((string) $categoryId) ? (int) $categoryId : null,
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
        ]);
    }

    public function update(UpdateAdminItemRequest $request, string $itemId): RedirectResponse
    {
        $item = $this->items->update((int) $itemId, new ItemUpdateData(
            name: $request->string('name')->value(),
            slug: $request->string('slug')->value(),
            code: $request->integer('code'),
            categoryId: $request->integer('category_id'),
            brandId: $request->filled('brand_id') ? $request->integer('brand_id') : null,
            brandIdProvided: $request->has('brand_id'),
            image: $request->file('image'),
            description: $request->filled('description') ? $request->string('description')->value() : null,
            descriptionProvided: $request->has('description'),
            status: $request->enum('status', GlobalStatusEnum::class),
            isAmazing: $request->boolean('is_amazing'),
            amazingEndTime: $request->filled('amazing_end_time')
                ? Verta::parseFormat('Y/m/d H:i:s', $request->string('amazing_end_time')->value())->toCarbon()
                : null,
            amazingEndTimeProvided: $request->has('amazing_end_time'),
            specifications: $request->specificationAnswers(),
            specificationsProvided: $request->boolean('specifications_present'),
        ));

        return redirect()
            ->route('admin.items.show', $item->id)
            ->with('success', 'آیتم با موفقیت ویرایش شد.');
    }

    public function specificationFields(int $categoryId): View
    {
        return view('admin.items.partials.specification-fields', [
            'specificationGroups' => $this->items->specificationGroups($categoryId),
            'item' => null,
            'selectedCategoryId' => $categoryId,
        ]);
    }
}
