<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Size\SizeCreateData;
use App\DTO\Size\SizeUpdateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminSizeRequest;
use App\Http\Requests\UpdateAdminSizeRequest;
use App\Services\Contracts\SizeServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SizeController extends Controller
{
    public function __construct(private readonly SizeServiceInterface $sizes)
    {
    }

    public function index(): View
    {
        return view('admin.sizes.index');
    }

    public function create(): View
    {
        return view('admin.sizes.create');
    }

    public function store(StoreAdminSizeRequest $request): RedirectResponse
    {
        $this->sizes->create(new SizeCreateData(
            value: $request->string('value')->value(),
        ));

        return redirect()
            ->route('admin.sizes.index')
            ->with('success', 'سایز جدید با موفقیت ایجاد شد.');
    }

    public function edit(string $sizeId): View
    {
        return view('admin.sizes.edit', [
            'size' => $this->sizes->details((int) $sizeId),
        ]);
    }

    public function update(UpdateAdminSizeRequest $request, string $sizeId): RedirectResponse
    {
        $this->sizes->update((int) $sizeId, new SizeUpdateData(
            value: $request->string('value')->value(),
        ));

        return redirect()
            ->route('admin.sizes.index')
            ->with('success', 'سایز با موفقیت ویرایش شد.');
    }
}
