<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Category\CategoryCreateData;
use App\DTO\Category\CategorySelectOptionsData;
use App\DTO\Category\CategoryUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminCategoryRequest;
use App\Http\Requests\CategorySelectOptionsRequest;
use App\Http\Requests\UpdateAdminCategoryRequest;
use App\Services\Contracts\CategoryServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryServiceInterface $categories)
    {
    }

    public function index(): View
    {
        return view('admin.categories.index');
    }

    public function selectOptions(CategorySelectOptionsRequest $request): JsonResponse
    {
        return response()->json(
            $this->categories->selectOptions(new CategorySelectOptionsData(
                search: $request->string('search')->value(),
                selectedIds: array_map('intval', $request->array('selected_ids')),
                page: $request->integer('page', 1),
            )),
        );
    }

    public function create(): View
    {
        return view('admin.categories.create', [
            ...$this->categories->formOptions(),
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
        ]);
    }

    public function store(StoreAdminCategoryRequest $request): RedirectResponse
    {
        $category = $this->categories->create(new CategoryCreateData(
            name: $request->string('name')->value(),
            parentId: $request->filled('parent_id') ? $request->integer('parent_id') : null,
            slug: $request->string('slug')->value(),
            priority: $request->integer('priority'),
            icon: $request->filled('icon') ? $request->string('icon')->value() : null,
            image: $request->file('image'),
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()
            ->route('admin.categories.show', $category->id)
            ->with('success', 'دسته‌بندی با موفقیت ایجاد شد.');
    }

    public function edit(string $categoryId): View
    {
        $categoryId = (int) $categoryId;

        return view('admin.categories.edit', [
            ...$this->categories->formOptions($categoryId),
            'category' => $this->categories->details($categoryId),
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
        ]);
    }

    public function update(UpdateAdminCategoryRequest $request, string $categoryId): RedirectResponse
    {
        $category = $this->categories->update((int) $categoryId, new CategoryUpdateData(
            name: $request->string('name')->value(),
            parentId: $request->filled('parent_id') ? $request->integer('parent_id') : null,
            parentIdProvided: $request->has('parent_id'),
            slug: $request->string('slug')->value(),
            priority: $request->integer('priority'),
            icon: $request->filled('icon') ? $request->string('icon')->value() : null,
            iconProvided: $request->has('icon'),
            image: $request->file('image'),
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()
            ->route('admin.categories.show', $category->id)
            ->with('success', 'دسته‌بندی با موفقیت ویرایش شد.');
    }

    public function show(string $categoryId): View
    {
        return view('admin.categories.show', [
            'category' => $this->categories->details((int) $categoryId),
        ]);
    }
}
