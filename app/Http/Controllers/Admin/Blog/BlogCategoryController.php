<?php

namespace App\Http\Controllers\Admin\Blog;

use App\DTO\Blog\BlogCategoryCreateData;
use App\DTO\Blog\BlogCategoryUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminBlogCategoryRequest;
use App\Http\Requests\UpdateAdminBlogCategoryRequest;
use App\Services\Contracts\BlogManagementServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class BlogCategoryController extends Controller
{
    public function __construct(private readonly BlogManagementServiceInterface $blog)
    {
    }

    public function index(): View
    {
        return view('admin.blog.categories.index');
    }

    public function create(): View
    {
        return view('admin.blog.categories.create', [
            'parents' => $this->blog->parentCategoryOptions(),
            'statusOptions' => $this->options(GlobalStatusEnum::toKeyValueType()),
        ]);
    }

    public function store(StoreAdminBlogCategoryRequest $request): RedirectResponse
    {
        $this->blog->createCategory(new BlogCategoryCreateData(
            name: $request->string('name')->value(),
            slug: $request->string('slug')->value(),
            parentId: $request->filled('parent_id') ? $request->integer('parent_id') : null,
            description: $request->filled('description') ? $request->string('description')->value() : null,
            image: $request->file('image'),
            priority: $request->integer('priority'),
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()->route('admin.admin-blog.categories.index')->with('success', 'دسته‌بندی وبلاگ ساخته شد.');
    }

    public function edit(string $categoryId): View
    {
        $category = $this->blog->categoryDetails((int) $categoryId);

        return view('admin.blog.categories.edit', [
            'category' => $category,
            'parents' => $this->blog->parentCategoryOptions($category->id),
            'statusOptions' => $this->options(GlobalStatusEnum::toKeyValueType()),
        ]);
    }

    public function update(UpdateAdminBlogCategoryRequest $request, string $categoryId): RedirectResponse
    {
        $this->blog->updateCategory((int) $categoryId, new BlogCategoryUpdateData(
            name: $request->string('name')->value(),
            slug: $request->string('slug')->value(),
            parentId: $request->filled('parent_id') ? $request->integer('parent_id') : null,
            parentIdProvided: $request->has('parent_id'),
            description: $request->filled('description') ? $request->string('description')->value() : null,
            descriptionProvided: $request->has('description'),
            image: $request->file('image'),
            removeImage: $request->boolean('remove_image'),
            priority: $request->integer('priority'),
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()->route('admin.admin-blog.categories.index')->with('success', 'دسته‌بندی وبلاگ ویرایش شد.');
    }

    public function destroy(string $categoryId): RedirectResponse
    {
        if (! $this->blog->deleteCategory((int) $categoryId)) {
            return redirect()->route('admin.admin-blog.categories.index')
                ->with('error', 'این دسته‌بندی نوشته دارد. برای حفظ نوشته‌ها، ابتدا آن‌ها را به دسته‌بندی دیگری منتقل کنید.');
        }

        return redirect()->route('admin.admin-blog.categories.index')
            ->with('success', 'دسته‌بندی وبلاگ حذف شد.');
    }

    /** @param array<int, \App\DTO\KeyValueType> $values
     *  @return array<string, string>
     */
    private function options(array $values): array
    {
        return collect($values)->mapWithKeys(fn ($item) => [$item->value => $item->key])->all();
    }
}
