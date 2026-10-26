<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Specification\SpecificationData;
use App\DTO\Specification\SpecificationGroupData;
use App\Enums\SpecificationTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\SaveSpecificationGroupRequest;
use App\Http\Requests\SaveSpecificationRequest;
use App\Services\Contracts\SpecificationServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SpecificationController extends Controller
{
    public function __construct(private readonly SpecificationServiceInterface $specifications)
    {
    }

    public function catalog(): View
    {
        return view('admin.specifications.catalog');
    }

    public function index(int $categoryId): View
    {
        return view('admin.specifications.index', [
            'category' => $this->specifications->category($categoryId),
            'groups' => $this->specifications->groups($categoryId),
        ]);
    }

    public function storeGroup(SaveSpecificationGroupRequest $request, int $categoryId): RedirectResponse
    {
        $this->specifications->createGroup($categoryId, new SpecificationGroupData(
            name: $request->string('name')->value(),
            sortOrder: $request->integer('sort_order'),
        ));

        return redirect()->route('admin.categories.specifications.index', $categoryId)
            ->with('success', 'گروه مشخصات ایجاد شد.');
    }

    public function updateGroup(SaveSpecificationGroupRequest $request, int $categoryId, int $groupId): RedirectResponse
    {
        $this->specifications->updateGroup($categoryId, $groupId, new SpecificationGroupData(
            name: $request->string('name')->value(),
            sortOrder: $request->integer('sort_order'),
        ));

        return redirect()->route('admin.categories.specifications.index', $categoryId)
            ->with('success', 'گروه مشخصات ویرایش شد.');
    }

    public function deleteGroup(int $categoryId, int $groupId): RedirectResponse
    {
        $this->specifications->deleteGroup($categoryId, $groupId);

        return redirect()->route('admin.categories.specifications.index', $categoryId)
            ->with('success', 'گروه مشخصات حذف شد.');
    }

    public function createField(int $categoryId): View
    {
        return view('admin.specifications.form', [
            'category' => $this->specifications->category($categoryId),
            'groups' => $this->specifications->groups($categoryId),
            'specification' => null,
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function storeField(SaveSpecificationRequest $request, int $categoryId): RedirectResponse
    {
        $this->specifications->createSpecification($categoryId, new SpecificationData(
            groupId: $request->integer('group_id'),
            key: $request->string('key')->value(),
            name: $request->string('name')->value(),
            type: $request->enum('type', SpecificationTypeEnum::class),
            unit: $request->filled('unit') ? $request->string('unit')->value() : null,
            isRequired: $request->boolean('is_required'),
            isHighlighted: $request->boolean('is_highlighted'),
            isFilterable: $request->boolean('is_filterable'),
            sortOrder: $request->integer('sort_order'),
            options: $request->filled('options') ? $request->string('options')->value() : null,
        ));

        return redirect()->route('admin.categories.specifications.index', $categoryId)
            ->with('success', 'مشخصه ایجاد شد.');
    }

    public function editField(int $categoryId, int $specificationId): View
    {
        return view('admin.specifications.form', [
            'category' => $this->specifications->category($categoryId),
            'groups' => $this->specifications->groups($categoryId),
            'specification' => $this->specifications->specification($categoryId, $specificationId),
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function updateField(SaveSpecificationRequest $request, int $categoryId, int $specificationId): RedirectResponse
    {
        $this->specifications->updateSpecification($categoryId, $specificationId, $this->data($request));

        return redirect()->route('admin.categories.specifications.index', $categoryId)
            ->with('success', 'مشخصه ویرایش شد.');
    }

    public function deleteField(int $categoryId, int $specificationId): RedirectResponse
    {
        $this->specifications->deleteSpecification($categoryId, $specificationId);

        return redirect()->route('admin.categories.specifications.index', $categoryId)
            ->with('success', 'مشخصه حذف شد.');
    }

    private function data(SaveSpecificationRequest $request): SpecificationData
    {
        return new SpecificationData(
            groupId: $request->integer('group_id'),
            key: $request->string('key')->value(),
            name: $request->string('name')->value(),
            type: $request->enum('type', SpecificationTypeEnum::class),
            unit: $request->filled('unit') ? $request->string('unit')->value() : null,
            isRequired: $request->boolean('is_required'),
            isHighlighted: $request->boolean('is_highlighted'),
            isFilterable: $request->boolean('is_filterable'),
            sortOrder: $request->integer('sort_order'),
            options: $request->filled('options') ? $request->string('options')->value() : null,
        );
    }

    /** @return array<string, string> */
    private function typeOptions(): array
    {
        return collect(SpecificationTypeEnum::toKeyValueType())
            ->mapWithKeys(fn ($item) => [$item->value => $item->key])->all();
    }
}
