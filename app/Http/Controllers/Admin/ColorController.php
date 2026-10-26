<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Color\ColorCreateData;
use App\DTO\Color\ColorUpdateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminColorRequest;
use App\Http\Requests\UpdateAdminColorRequest;
use App\Services\Contracts\ColorServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ColorController extends Controller
{
    public function __construct(private readonly ColorServiceInterface $colors)
    {
    }

    public function index(): View
    {
        return view('admin.colors.index');
    }

    public function create(): View
    {
        return view('admin.colors.create');
    }

    public function store(StoreAdminColorRequest $request): RedirectResponse
    {
        $this->colors->create(new ColorCreateData(
            name: $request->string('name')->value(),
            value: $request->string('value')->value(),
        ));

        return redirect()
            ->route('admin.colors.index')
            ->with('success', 'رنگ جدید با موفقیت ایجاد شد.');
    }

    public function edit(string $colorId): View
    {
        return view('admin.colors.edit', [
            'color' => $this->colors->details((int) $colorId),
        ]);
    }

    public function update(UpdateAdminColorRequest $request, string $colorId): RedirectResponse
    {
        $this->colors->update((int) $colorId, new ColorUpdateData(
            name: $request->string('name')->value(),
            value: $request->string('value')->value(),
        ));

        return redirect()
            ->route('admin.colors.index')
            ->with('success', 'رنگ با موفقیت ویرایش شد.');
    }
}
