<?php

namespace App\Http\Controllers\Admin;

use App\DTO\City\CityCreateData;
use App\DTO\City\CityUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminCityRequest;
use App\Http\Requests\UpdateAdminCityRequest;
use App\Services\Contracts\CityServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CityController extends Controller
{
    public function __construct(private readonly CityServiceInterface $cities)
    {
    }

    public function index(): View
    {
        return view('admin.cities.index');
    }

    public function create(): View
    {
        return view('admin.cities.create', [
            ...$this->cities->formOptions(),
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function store(StoreAdminCityRequest $request): RedirectResponse
    {
        $city = $this->cities->create(new CityCreateData(
            provinceId: $request->integer('province_id'),
            name: $request->string('name')->value(),
            slug: $request->string('slug')->value(),
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()
            ->route('admin.cities.show', $city->id)
            ->with('success', 'شهر با موفقیت ایجاد شد.');
    }

    public function edit(string $cityId): View
    {
        $city = $this->cities->details((int) $cityId);

        return view('admin.cities.edit', [
            ...$this->cities->formOptions(),
            'city' => $city,
            'statusOptions' => $this->statusOptions(),
        ]);
    }

    public function update(UpdateAdminCityRequest $request, string $cityId): RedirectResponse
    {
        $city = $this->cities->update((int) $cityId, new CityUpdateData(
            provinceId: $request->integer('province_id'),
            name: $request->string('name')->value(),
            slug: $request->string('slug')->value(),
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()
            ->route('admin.cities.show', $city->id)
            ->with('success', 'شهر با موفقیت ویرایش شد.');
    }

    public function show(string $cityId): View
    {
        return view('admin.cities.show', [
            'city' => $this->cities->details((int) $cityId),
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
