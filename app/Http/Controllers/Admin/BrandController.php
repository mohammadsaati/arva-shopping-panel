<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Brand\BrandCreateData;
use App\DTO\Brand\BrandUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminBrandRequest;
use App\Http\Requests\UpdateAdminBrandRequest;
use App\Services\Contracts\BrandServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class BrandController extends Controller
{
    public function __construct(private readonly BrandServiceInterface $brands)
    {
    }

    public function index(): View
    {
        return view('admin.brands.index');
    }

    public function create(): View
    {
        return view('admin.brands.create', [
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
        ]);
    }

    public function store(StoreAdminBrandRequest $request): RedirectResponse
    {
        $brand = $this->brands->create(new BrandCreateData(
            name: $request->string('name')->value(),
            slug: $request->string('slug')->value(),
            image: $request->file('image'),
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'برند «' . $brand->name . '» با موفقیت ایجاد شد.');
    }

    public function edit(string $brandId): View
    {
        return view('admin.brands.edit', [
            'brand' => $this->brands->details((int) $brandId),
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
        ]);
    }

    public function update(UpdateAdminBrandRequest $request, string $brandId): RedirectResponse
    {
        $brand = $this->brands->update((int) $brandId, new BrandUpdateData(
            name: $request->string('name')->value(),
            slug: $request->string('slug')->value(),
            image: $request->file('image'),
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()
            ->route('admin.brands.index')
            ->with('success', 'برند «' . $brand->name . '» با موفقیت ویرایش شد.');
    }
}
