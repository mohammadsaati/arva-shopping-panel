<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Banner\BannerCreateData;
use App\DTO\Banner\BannerUpdateData;
use App\Enums\BannerTypeEnum;
use App\Enums\GlobalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminBannerRequest;
use App\Http\Requests\UpdateAdminBannerRequest;
use App\Services\Contracts\BannerServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class BannerController extends Controller
{
    public function __construct(private readonly BannerServiceInterface $banners)
    {
    }

    public function index(): View
    {
        return view('admin.banners.index');
    }

    public function create(): View
    {
        return view('admin.banners.create', [
            ...$this->banners->formOptions(),
            'typeOptions' => $this->options(BannerTypeEnum::toKeyValueType()),
            'statusOptions' => $this->options(GlobalStatusEnum::toKeyValueType()),
        ]);
    }

    public function store(StoreAdminBannerRequest $request): RedirectResponse
    {
        $type = $request->enum('type', BannerTypeEnum::class);
        $this->banners->create(new BannerCreateData(
            image: $request->file('image'),
            type: $type,
            productId: $type === BannerTypeEnum::PRODUCT && $request->filled('product_id') ? $request->integer('product_id') : null,
            categoryId: $type === BannerTypeEnum::CATEGORY && $request->filled('category_id') ? $request->integer('category_id') : null,
            link: $type === BannerTypeEnum::LINK ? $request->string('link')->value() : null,
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()->route('admin.banners.index')->with('success', 'بنر با موفقیت ایجاد شد.');
    }

    public function edit(string $bannerId): View
    {
        $banner = $this->banners->details((int) $bannerId);

        return view('admin.banners.edit', [
            'banner' => $banner,
            ...$this->banners->formOptions($banner->product_id, $banner->category_id),
            'typeOptions' => $this->options(BannerTypeEnum::toKeyValueType()),
            'statusOptions' => $this->options(GlobalStatusEnum::toKeyValueType()),
        ]);
    }

    public function update(UpdateAdminBannerRequest $request, string $bannerId): RedirectResponse
    {
        $type = $request->enum('type', BannerTypeEnum::class);
        $this->banners->update((int) $bannerId, new BannerUpdateData(
            image: $request->file('image'),
            type: $type,
            productId: $type === BannerTypeEnum::PRODUCT && $request->filled('product_id') ? $request->integer('product_id') : null,
            productIdProvided: true,
            categoryId: $type === BannerTypeEnum::CATEGORY && $request->filled('category_id') ? $request->integer('category_id') : null,
            categoryIdProvided: true,
            link: $type === BannerTypeEnum::LINK && $request->filled('link') ? $request->string('link')->value() : null,
            linkProvided: true,
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()->route('admin.banners.index')->with('success', 'بنر با موفقیت ویرایش شد.');
    }

    /** @param array<int, \App\DTO\KeyValueType> $values
     *  @return array<string, string>
     */
    private function options(array $values): array
    {
        return collect($values)->mapWithKeys(fn ($item) => [$item->value => $item->key])->all();
    }
}
