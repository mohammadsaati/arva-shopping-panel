<x-user-panel :title="$category->name" sub-title="محصولات و زیر‌دسته‌های این دسته‌بندی را ببینید.">
    <div class="store-category-page container">
        <nav class="store-shop__breadcrumbs" aria-label="مسیر صفحه">
            <a href="{{ route('home') }}">صفحه اصلی</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('shop.index') }}">فروشگاه</a>
            @if ($category->parent)
                <span aria-hidden="true">/</span>
                <a href="{{ route('categories.show', ['slug' => $category->parent->slug]) }}">{{ $category->parent->name }}</a>
            @endif
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ $category->name }}</span>
        </nav>

        <div class="store-category-page__hero">
            <div class="store-category-page__identity">
                <span class="store-category-page__icon"><i class="{{ $category->icon ?: 'ki-filled ki-category' }}" aria-hidden="true"></i></span>
                <div>
                    <p>دسته‌بندی {{ site_name() }}</p>
                    <h2>{{ $category->name }}</h2>
                    <span>مدل‌های این دسته‌بندی را با فیلترهای فروشگاه پیدا کنید.</span>
                </div>
            </div>
            @if ($category->image)
                <img src="{{ public_image_url($category->image) }}" alt="" loading="lazy">
            @endif
        </div>

        @if ($category->children->isNotEmpty())
            <section class="store-category-page__children" aria-labelledby="category-children-title">
                <div class="store-section__heading">
                    <div>
                        <p class="store-section__eyebrow">دسته‌بندی‌های مرتبط</p>
                        <h2 id="category-children-title" class="store-section__title">زیر‌دسته‌های {{ $category->name }}</h2>
                    </div>
                </div>
                <div class="store-category-grid">
                    @foreach ($category->children as $childCategory)
                        <a class="store-category-card" href="{{ route('categories.show', ['slug' => $childCategory->slug]) }}">
                            <div class="store-category-card__image">
                                @if ($childCategory->image)
                                    <img src="{{ public_image_url($childCategory->image) }}" alt="" loading="lazy">
                                @else
                                    <i class="{{ $childCategory->icon ?: 'ki-filled ki-category' }}" aria-hidden="true"></i>
                                @endif
                            </div>
                            <h3>{{ $childCategory->name }}</h3>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <livewire:storefront.catalog :fixed-category-id="$category->id" />
    </div>
</x-user-panel>
