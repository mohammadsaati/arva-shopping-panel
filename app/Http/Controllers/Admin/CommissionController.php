<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Commission\CommissionCreateData;
use App\DTO\Commission\CommissionUpdateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommissionRequest;
use App\Http\Requests\UpdateCommissionRequest;
use App\Services\Contracts\CommissionServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CommissionController extends Controller
{
    public function __construct(private readonly CommissionServiceInterface $commissions)
    {
    }

    public function index(): View
    {
        return view('admin.commissions.index');
    }

    public function create(): View
    {
        return view('admin.commissions.create');
    }

    public function store(StoreCommissionRequest $request): RedirectResponse
    {
        $this->commissions->create(new CommissionCreateData(
            title: $request->string('title')->value(),
            percentage: $request->string('percentage')->value(),
        ));

        return redirect()
            ->route('admin.commissions.index')
            ->with('success', 'کمیسیون با موفقیت ایجاد شد.');
    }

    public function edit(string $commissionId): View
    {
        return view('admin.commissions.edit', [
            'commission' => $this->commissions->details((int) $commissionId),
        ]);
    }

    public function update(UpdateCommissionRequest $request, string $commissionId): RedirectResponse
    {
        $this->commissions->update((int) $commissionId, new CommissionUpdateData(
            title: $request->string('title')->value(),
            percentage: $request->string('percentage')->value(),
        ));

        return redirect()
            ->route('admin.commissions.index')
            ->with('success', 'کمیسیون با موفقیت ویرایش شد.');
    }
}
