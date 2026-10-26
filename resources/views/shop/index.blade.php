<x-user-layout :title="'فروشگاه '.site_name().' | محصولات'">
    <section class="store-shop container">
        <nav class="store-shop__breadcrumbs" aria-label="مسیر صفحه">
            <a href="{{ route('home') }}">صفحه اصلی</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page">فروشگاه</span>
        </nav>

        <header class="store-shop__heading">
            <div>
                <p class="store-section__eyebrow">همه انتخاب‌های {{ site_name() }}</p>
                <h1 class="store-section__title">فروشگاه محصولات</h1>
                <p>کالاها را جستجو کنید و با فیلترهای کنار صفحه، انتخاب خود را دقیق‌تر کنید.</p>
            </div>
            <a class="store-shop__home-link" href="{{ route('home') }}">بازگشت به صفحه اصلی</a>
        </header>

        <livewire:storefront.catalog />
    </section>
</x-user-layout>
