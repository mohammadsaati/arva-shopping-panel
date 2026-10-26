<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Slider\SliderCreateData;
use App\DTO\Slider\SliderUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Enums\SliderTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminSliderRequest;
use App\Http\Requests\UpdateAdminSliderRequest;
use App\Services\Contracts\SliderServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SliderController extends Controller
{
    public function __construct(private readonly SliderServiceInterface $sliders)
    {
    }

    public function index(): View
    {
        return view('admin.sliders.index');
    }

    public function create(): View
    {
        return view('admin.sliders.create', [
            ...$this->sliders->formOptions(),
            'typeOptions' => $this->options(SliderTypeEnum::toKeyValueType()),
            'statusOptions' => $this->options(GlobalStatusEnum::toKeyValueType()),
        ]);
    }

    public function store(StoreAdminSliderRequest $request): RedirectResponse
    {
        $type = $request->enum('type', SliderTypeEnum::class);
        $this->sliders->create(new SliderCreateData(
            title: $request->string('title')->value(),
            priority: $request->integer('priority'),
            image: $request->file('image'),
            type: $type,
            productId: $type === SliderTypeEnum::PRODUCT && $request->filled('product_id') ? $request->integer('product_id') : null,
            categoryId: $type === SliderTypeEnum::CATEGORY && $request->filled('category_id') ? $request->integer('category_id') : null,
            sectionId: $type === SliderTypeEnum::SESSION && $request->filled('section_id') ? $request->integer('section_id') : null,
            link: $type === SliderTypeEnum::LINK ? $request->string('link')->value() : null,
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()->route('admin.sliders.index')->with('success', 'اسلایدر با موفقیت ایجاد شد.');
    }

    public function edit(string $sliderId): View
    {
        $slider = $this->sliders->details((int) $sliderId);

        return view('admin.sliders.edit', [
            'slider' => $slider,
            ...$this->sliders->formOptions($slider->product_id, $slider->category_id, $slider->section_id),
            'typeOptions' => $this->options(SliderTypeEnum::toKeyValueType()),
            'statusOptions' => $this->options(GlobalStatusEnum::toKeyValueType()),
        ]);
    }

    public function update(UpdateAdminSliderRequest $request, string $sliderId): RedirectResponse
    {
        $type = $request->enum('type', SliderTypeEnum::class);
        $this->sliders->update((int) $sliderId, new SliderUpdateData(
            title: $request->string('title')->value(),
            priority: $request->integer('priority'),
            image: $request->file('image'),
            type: $type,
            productId: $type === SliderTypeEnum::PRODUCT && $request->filled('product_id') ? $request->integer('product_id') : null,
            productIdProvided: true,
            categoryId: $type === SliderTypeEnum::CATEGORY && $request->filled('category_id') ? $request->integer('category_id') : null,
            categoryIdProvided: true,
            sectionId: $type === SliderTypeEnum::SESSION && $request->filled('section_id') ? $request->integer('section_id') : null,
            sectionIdProvided: true,
            link: $type === SliderTypeEnum::LINK && $request->filled('link') ? $request->string('link')->value() : null,
            linkProvided: true,
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()->route('admin.sliders.index')->with('success', 'اسلایدر با موفقیت ویرایش شد.');
    }

    /** @param array<int, \App\DTO\KeyValueType> $values
     *  @return array<string, string>
     */
    private function options(array $values): array
    {
        return collect($values)->mapWithKeys(fn ($item) => [$item->value => $item->key])->all();
    }
}
