<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Province\ProvinceCreateData;
use App\DTO\Province\ProvinceUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminProvinceRequest;
use App\Http\Requests\UpdateAdminProvinceRequest;
use App\Services\Contracts\ProvinceServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProvinceController extends Controller
{
    public function __construct(private readonly ProvinceServiceInterface $provinces)
    {
    }

    public function index(): View
    {
        return view('admin.provinces.index');
    }

    public function create(): View
    {
        return view('admin.provinces.create', [
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function store(StoreAdminProvinceRequest $request): RedirectResponse
    {
        $province = $this->provinces->create(new ProvinceCreateData(
            name: $request->string('name')->value(),
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()
            ->route('admin.provinces.show', $province->id)
            ->with('success', 'استان با موفقیت ایجاد شد.');
    }

    public function edit(string $provinceId): View
    {
        return view('admin.provinces.edit', [
            'province' => $this->provinces->details((int) $provinceId),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function update(UpdateAdminProvinceRequest $request, string $provinceId): RedirectResponse
    {
        $province = $this->provinces->update((int) $provinceId, new ProvinceUpdateData(
            name: $request->string('name')->value(),
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()
            ->route('admin.provinces.show', $province->id)
            ->with('success', 'استان با موفقیت ویرایش شد.');
    }

    public function show(string $provinceId): View
    {
        return view('admin.provinces.show', [
            'province' => $this->provinces->details((int) $provinceId),
        ]);
    }

    /** @return array<string, string> */
    private function statusOptions(): array
    {
        return collect(GlobalStatusEnum::toKeyValueType())
            ->mapWithKeys(fn ($item) => [$item->value => $item->key])
            ->all();
    }
}
