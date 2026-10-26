<?php

namespace App\Http\Controllers\Admin\Blog;

use App\DTO\Blog\BlogPostCreateData;
use App\DTO\Blog\BlogPostUpdateData;
use App\Enums\BlogPostStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminBlogPostRequest;
use App\Http\Requests\UpdateAdminBlogPostRequest;
use App\Services\Contracts\BlogManagementServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class BlogPostController extends Controller
{
    public function __construct(private readonly BlogManagementServiceInterface $blog)
    {
    }

    public function index(): View
    {
        return view('admin.blog.posts.index');
    }

    public function create(): View
    {
        return view('admin.blog.posts.create', [
            'categories' => $this->blog->postCategoryOptions(),
            'statusOptions' => $this->options(BlogPostStatusEnum::toKeyValueType()),
        ]);
    }

    public function store(StoreAdminBlogPostRequest $request): RedirectResponse
    {
        $this->blog->createPost(new BlogPostCreateData(
            blogCategoryId: $request->integer('blog_category_id'),
            authorId: auth('admin')->id(),
            title: $request->string('title')->value(),
            slug: $request->string('slug')->value(),
            excerpt: $request->filled('excerpt') ? $request->string('excerpt')->value() : null,
            body: $request->filled('body') ? $request->string('body')->value() : null,
            featuredImage: $request->file('featured_image'),
            status: $request->enum('status', BlogPostStatusEnum::class),
            isFeatured: $request->boolean('is_featured'),
            featuredOrder: $request->integer('featured_order'),
            metaTitle: $request->filled('meta_title') ? $request->string('meta_title')->value() : null,
            metaDescription: $request->filled('meta_description') ? $request->string('meta_description')->value() : null,
            publishedAt: $request->publishedAt(),
        ));

        return redirect()->route('admin.admin-blog.posts.index')->with('success', 'نوشتهٔ وبلاگ ایجاد شد.');
    }

    public function show(string $postId): View
    {
        return view('admin.blog.posts.show', [
            'post' => $this->blog->postDetails((int) $postId),
        ]);
    }

    public function edit(string $postId): View
    {
        return view('admin.blog.posts.edit', [
            'post' => $this->blog->postDetails((int) $postId),
            'categories' => $this->blog->postCategoryOptions(),
            'statusOptions' => $this->options(BlogPostStatusEnum::toKeyValueType()),
        ]);
    }

    public function update(UpdateAdminBlogPostRequest $request, string $postId): RedirectResponse
    {
        $this->blog->updatePost((int) $postId, new BlogPostUpdateData(
            blogCategoryId: $request->integer('blog_category_id'),
            title: $request->string('title')->value(),
            slug: $request->string('slug')->value(),
            excerpt: $request->filled('excerpt') ? $request->string('excerpt')->value() : null,
            excerptProvided: $request->has('excerpt'),
            body: $request->filled('body') ? $request->string('body')->value() : null,
            bodyProvided: $request->has('body'),
            featuredImage: $request->file('featured_image'),
            removeFeaturedImage: $request->boolean('remove_featured_image'),
            status: $request->enum('status', BlogPostStatusEnum::class),
            isFeatured: $request->boolean('is_featured'),
            featuredOrder: $request->integer('featured_order'),
            metaTitle: $request->filled('meta_title') ? $request->string('meta_title')->value() : null,
            metaTitleProvided: $request->has('meta_title'),
            metaDescription: $request->filled('meta_description') ? $request->string('meta_description')->value() : null,
            metaDescriptionProvided: $request->has('meta_description'),
            publishedAt: $request->publishedAt(),
            publishedAtProvided: $request->has('published_at'),
        ));

        return redirect()->route('admin.admin-blog.posts.index')->with('success', 'نوشتهٔ وبلاگ ویرایش شد.');
    }

    public function destroy(string $postId): RedirectResponse
    {
        $this->blog->deletePost((int) $postId);

        return redirect()->route('admin.admin-blog.posts.index')->with('success', 'نوشتهٔ وبلاگ حذف شد.');
    }

    /** @param array<int, \App\DTO\KeyValueType> $values
     *  @return array<string, string>
     */
    private function options(array $values): array
    {
        return collect($values)->mapWithKeys(fn ($item) => [$item->value => $item->key])->all();
    }
}
