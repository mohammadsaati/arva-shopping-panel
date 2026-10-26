<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Section\SectionCreateData;
use App\DTO\Section\SectionUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminSectionRequest;
use App\Http\Requests\UpdateAdminSectionRequest;
use App\Services\Contracts\SectionServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SectionController extends Controller
{
    public function __construct(private readonly SectionServiceInterface $sections)
    {
    }

    public function index(): View
    {
        return view('admin.sections.index');
    }

    public function create(): View
    {
        return view('admin.sections.create', [
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function store(StoreAdminSectionRequest $request): RedirectResponse
    {
        $this->sections->create(new SectionCreateData(
            name: $request->string('name')->value(),
            slug: $request->string('slug')->value(),
            bgColor: $request->filled('bg_color') ? $request->string('bg_color')->value() : null,
            bgImage: $request->file('bg_image'),
            images: $request->file('images') ?? [],
            status: $request->enum('status', GlobalStatusEnum::class),
            itemIds: array_map('intval', $request->input('item_ids', []) ?? []),
        ));

        return redirect()->route('admin.sections.index')->with('success', 'بخش فروشگاه با موفقیت ایجاد شد.');
    }

    public function edit(string $sectionId): View
    {
        $section = $this->sections->details((int) $sectionId);

        return view('admin.sections.edit', [
            'section' => $section,
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function update(UpdateAdminSectionRequest $request, string $sectionId): RedirectResponse
    {
        $this->sections->update((int) $sectionId, new SectionUpdateData(
            name: $request->string('name')->value(),
            slug: $request->string('slug')->value(),
            bgColor: $request->filled('bg_color') ? $request->string('bg_color')->value() : null,
            bgColorProvided: $request->has('bg_color'),
            bgImage: $request->file('bg_image'),
            removeBgImage: $request->boolean('remove_bg_image'),
            images: $request->file('images') ?? [],
            deleteImageIds: array_map('intval', $request->input('delete_image_ids', []) ?? []),
            status: $request->enum('status', GlobalStatusEnum::class),
            itemIds: array_map('intval', $request->input('item_ids', []) ?? []),
            itemIdsProvided: true,
        ));

        return redirect()->route('admin.sections.index')->with('success', 'بخش فروشگاه با موفقیت ویرایش شد.');
    }

    /** @return array<string, string> */
    private function statusOptions(): array
    {
        return collect(GlobalStatusEnum::toKeyValueType())
            ->mapWithKeys(fn ($item) => [$item->value => $item->key])
            ->all();
    }
}
