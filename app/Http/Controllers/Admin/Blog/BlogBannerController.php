<?php

namespace App\Http\Controllers\Admin\Blog;

use App\DTO\Blog\BlogBannerCreateData;
use App\DTO\Blog\BlogBannerUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminBlogBannerRequest;
use App\Http\Requests\UpdateAdminBlogBannerRequest;
use App\Services\Contracts\BlogManagementServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class BlogBannerController extends Controller
{
    public function __construct(private readonly BlogManagementServiceInterface $blog)
    {
    }

    public function index(): View
    {
        return view('admin.blog.banners.index');
    }

    public function create(): View
    {
        return view('admin.blog.banners.create', [
            'statusOptions' => $this->options(GlobalStatusEnum::toKeyValueType()),
        ]);
    }

    public function store(StoreAdminBlogBannerRequest $request): RedirectResponse
    {
        $this->blog->createBanner(new BlogBannerCreateData(
            eyebrow: $request->filled('eyebrow') ? $request->string('eyebrow')->value() : null,
            title: $request->string('title')->value(),
            description: $request->filled('description') ? $request->string('description')->value() : null,
            image: $request->file('image'),
            mobileImage: $request->file('mobile_image'),
            targetUrl: $request->filled('target_url') ? $request->string('target_url')->value() : null,
            targetLabel: $request->filled('target_label') ? $request->string('target_label')->value() : null,
            sortOrder: $request->integer('sort_order'),
            startsAt: $request->startsAt(),
            endsAt: $request->endsAt(),
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()->route('admin.admin-blog.banners.index')->with('success', 'بنر وبلاگ ایجاد شد.');
    }

    public function edit(string $bannerId): View
    {
        return view('admin.blog.banners.edit', [
            'banner' => $this->blog->bannerDetails((int) $bannerId),
            'statusOptions' => $this->options(GlobalStatusEnum::toKeyValueType()),
        ]);
    }

    public function update(UpdateAdminBlogBannerRequest $request, string $bannerId): RedirectResponse
    {
        $this->blog->updateBanner((int) $bannerId, new BlogBannerUpdateData(
            eyebrow: $request->filled('eyebrow') ? $request->string('eyebrow')->value() : null,
            eyebrowProvided: $request->has('eyebrow'),
            title: $request->string('title')->value(),
            description: $request->filled('description') ? $request->string('description')->value() : null,
            descriptionProvided: $request->has('description'),
            image: $request->file('image'),
            mobileImage: $request->file('mobile_image'),
            removeMobileImage: $request->boolean('remove_mobile_image'),
            targetUrl: $request->filled('target_url') ? $request->string('target_url')->value() : null,
            targetUrlProvided: $request->has('target_url'),
            targetLabel: $request->filled('target_label') ? $request->string('target_label')->value() : null,
            targetLabelProvided: $request->has('target_label'),
            sortOrder: $request->integer('sort_order'),
            startsAt: $request->startsAt(),
            startsAtProvided: $request->has('starts_at'),
            endsAt: $request->endsAt(),
            endsAtProvided: $request->has('ends_at'),
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return redirect()->route('admin.admin-blog.banners.index')->with('success', 'بنر وبلاگ ویرایش شد.');
    }

    public function destroy(string $bannerId): RedirectResponse
    {
        $this->blog->deleteBanner((int) $bannerId);

        return redirect()->route('admin.admin-blog.banners.index')->with('success', 'بنر وبلاگ حذف شد.');
    }

    /** @param array<int, \App\DTO\KeyValueType> $values
     *  @return array<string, string>
     */
    private function options(array $values): array
    {
        return collect($values)->mapWithKeys(fn ($item) => [$item->value => $item->key])->all();
    }
}
