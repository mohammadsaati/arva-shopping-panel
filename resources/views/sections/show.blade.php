<x-user-panel :title="$section->name" sub-title="همه کالاهای این بخش را مرور کنید.">
    <div class="store-section-page container">
        <nav class="store-shop__breadcrumbs" aria-label="مسیر صفحه">
            <a href="{{ route('home') }}">صفحه اصلی</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">{{ $section->name }}</span>
        </nav>

        @if ($section->bg_image)
            <section
                class="store-section-page__hero"
                aria-labelledby="section-hero-title"
                style="--store-section-hero-image: url('{{ public_image_url($section->bg_image) }}')"
            >
                <div class="store-section-page__hero-content">
                    <p>مجموعهٔ {{ site_name() }}</p>
                    <h2 id="section-hero-title">پیشنهادهایی برای شما</h2>
                    <a href="#section-products">مشاهده محصولات <span aria-hidden="true">←</span></a>
                </div>
            </section>
        @endif

        <div class="store-section-page__heading store-karin-section__heading">
            <div class="store-karin-section__identity">
                <span class="store-karin-section__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 9h16l-1.2 11H5.2L4 9Z" />
                        <path d="M8 10V6a4 4 0 0 1 8 0v4" />
                        <path d="M9 14h6" />
                    </svg>
                </span>
                <div>
                    <h2 class="store-karin-section__title">محصولات <span>{{ $section->name }}</span></h2>
                    <p class="store-karin-section__subtitle">تمام کالاهای این بخش</p>
                </div>
            </div>
        </div>

        <div id="section-products">
            <livewire:storefront.section-items :section-id="$section->id" />
        </div>
    </div>
</x-user-panel>
