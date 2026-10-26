@php
    $adminName = auth('admin')->user()?->name ?: 'مدیر';
    $hour = (int) now('Asia/Tehran')->format('G');
    $greeting = match (true) {
        $hour < 12 => 'صبح بخیر',
        $hour < 18 => 'روز بخیر',
        default => 'عصر بخیر',
    };
@endphp

<div class="admin-dash">
    <section class="admin-dash__hero" aria-label="خوش‌آمدگویی داشبورد">
        <div class="admin-dash__hero-copy">
            <p class="admin-dash__eyebrow">مرکز فرمان فروشگاه</p>
            <h2>{{ $greeting }}، {{ $adminName }}</h2>
            <p>اینجا فقط کارهای مهم و نمونه‌های محدود می‌آید تا با دیتای زیاد هم پنل سبک بماند.</p>
            <div class="admin-dash__hero-actions">
                <x-button href="{{ route('admin.orders.index') }}" size="sm" icon="ki-filled ki-basket">رسیدگی به سفارش‌ها</x-button>
                <x-button href="{{ route('admin.items.create') }}" variant="light" size="sm" icon="ki-filled ki-plus">افزودن آیتم</x-button>
            </div>
        </div>
        <div class="admin-dash__hero-orbits" aria-hidden="true">
            <span class="admin-dash__orb admin-dash__orb--a"></span>
            <span class="admin-dash__orb admin-dash__orb--b"></span>
            <span class="admin-dash__spark admin-dash__spark--a"></span>
            <span class="admin-dash__spark admin-dash__spark--b"></span>
            <span class="admin-dash__spark admin-dash__spark--c"></span>
        </div>
    </section>

    <section class="admin-dash__launch" aria-label="میانبرهای مدیریت">
        <a class="admin-dash__mission admin-dash__mission--orders" href="{{ route('admin.orders.index') }}">
            <span class="admin-dash__mission-icon"><i class="ki-filled ki-basket"></i></span>
            <strong>سفارش‌ها</strong>
            <em>وضعیت و پرداخت را سریع بررسی کنید</em>
        </a>
        <a class="admin-dash__mission admin-dash__mission--catalog" href="{{ route('admin.items.create') }}">
            <span class="admin-dash__mission-icon"><i class="ki-filled ki-plus-squared"></i></span>
            <strong>آیتم تازه</strong>
            <em>کالای جدید را به کاتالوگ اضافه کنید</em>
        </a>
        <a class="admin-dash__mission admin-dash__mission--products" href="{{ route('admin.products.index') }}">
            <span class="admin-dash__mission-icon"><i class="ki-filled ki-package"></i></span>
            <strong>محصولات</strong>
            <em>قیمت، موجودی و تنوع‌ها</em>
        </a>
        <a class="admin-dash__mission admin-dash__mission--reviews" href="{{ route('admin.product-reviews.index') }}">
            <span class="admin-dash__mission-icon"><i class="ki-filled ki-message-text-2"></i></span>
            <strong>نظرات</strong>
            <em>تأیید یا رد بازخورد مشتریان</em>
        </a>
        <a class="admin-dash__mission admin-dash__mission--blog" href="{{ route('admin.admin-blog.posts.index') }}">
            <span class="admin-dash__mission-icon"><i class="ki-filled ki-notepad-edit"></i></span>
            <strong>وبلاگ</strong>
            <em>نوشته‌ها و محتوای مجله</em>
        </a>
        <a class="admin-dash__mission admin-dash__mission--settings" href="{{ route('admin.settings.edit') }}">
            <span class="admin-dash__mission-icon"><i class="ki-filled ki-setting-2"></i></span>
            <strong>تنظیمات</strong>
            <em>هویت سایت، فوتر و ارتباط</em>
        </a>
    </section>

    <div class="admin-dash__boards">
        <x-datatable title="سفارش‌های در انتظار" description="فقط چند نمونه آخر؛ بدون جمع کل و بدون فشار روی دیتابیس">
            <x-slot:actions><x-button href="{{ route('admin.orders.index') }}" variant="light" size="sm">همه سفارش‌ها</x-button></x-slot:actions>
            <x-slot:head>
                <x-datatable-heading>سفارش</x-datatable-heading>
                <x-datatable-heading>مشتری</x-datatable-heading>
                <x-datatable-heading>مبلغ</x-datatable-heading>
                <x-datatable-heading>تاریخ</x-datatable-heading>
                <x-datatable-heading class="w-20 text-end">جزئیات</x-datatable-heading>
            </x-slot:head>
            @forelse ($overview->pendingOrders as $order)
                <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                    <td class="px-5 py-3 font-mono font-medium">#{{ $order->id }}</td>
                    <td class="px-5 py-3">{{ $order->user->first_name }} {{ $order->user->last_name }}</td>
                    <td class="px-5 py-3 whitespace-nowrap">{{ number_format($order->amount) }} تومان</td>
                    <td class="px-5 py-3 whitespace-nowrap text-gray-500">{{ jalali_date($order->created_at) ?? '—' }}</td>
                    <td class="px-5 py-3 text-end"><x-button href="{{ route('admin.orders.show', $order->id) }}" variant="light" size="sm" icon="ki-filled ki-eye" aria-label="جزئیات سفارش {{ $order->id }}" /></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">سفارش در انتظاری نیست؛ عالی است.</td></tr>
            @endforelse
        </x-datatable>

        <x-datatable title="کم‌موجودی‌ها" description="نمونه‌های فعال با موجودی ۵ عدد یا کمتر">
            <x-slot:actions><x-button href="{{ route('admin.products.index', ['stock' => \App\Enums\ProductStockFilterEnum::LOW_STOCK->value]) }}" variant="light" size="sm">موجودی‌های کم</x-button></x-slot:actions>
            <x-slot:head>
                <x-datatable-heading>محصول</x-datatable-heading>
                <x-datatable-heading>موجودی</x-datatable-heading>
                <x-datatable-heading class="w-20 text-end">ویرایش</x-datatable-heading>
            </x-slot:head>
            @forelse ($overview->lowStockProducts as $product)
                <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                    <td class="px-5 py-3"><span class="block font-medium">{{ $product->item->name }}</span><span class="text-xs text-gray-500">#{{ $product->id }}</span></td>
                    <td class="px-5 py-3"><x-badge :variant="$product->stock === 0 ? 'danger' : 'warning'" size="sm">{{ $product->stock }}</x-badge></td>
                    <td class="px-5 py-3 text-end"><x-button href="{{ route('admin.products.edit', $product->id) }}" variant="light" size="sm" icon="ki-filled ki-notepad-edit" aria-label="ویرایش محصول {{ $product->item->name }}" /></td>
                </tr>
            @empty
                <tr><td colspan="3" class="px-5 py-10 text-center text-gray-400">فعلاً کم‌موجودی نمونه‌ای نیست.</td></tr>
            @endforelse
        </x-datatable>

        <x-datatable title="نظرات منتظر تأیید" description="چند بازخورد آخر برای رسیدگی سریع">
            <x-slot:actions><x-button href="{{ route('admin.product-reviews.index') }}" variant="light" size="sm">همه نظرات</x-button></x-slot:actions>
            <x-slot:head>
                <x-datatable-heading>محصول</x-datatable-heading>
                <x-datatable-heading>امتیاز</x-datatable-heading>
                <x-datatable-heading>نویسنده</x-datatable-heading>
                <x-datatable-heading class="w-20 text-end">بررسی</x-datatable-heading>
            </x-slot:head>
            @forelse ($overview->pendingReviews as $review)
                <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                    <td class="px-5 py-3">
                        <span class="block font-medium">{{ $review->item->name }}</span>
                        <span class="text-xs text-gray-500 line-clamp-1">{{ $review->title ?: \Illuminate\Support\Str::limit($review->body, 42) }}</span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">{{ $review->rating }} از ۵</td>
                    <td class="px-5 py-3">{{ $review->user?->first_name ? trim($review->user->first_name.' '.$review->user->last_name) : ($review->guest_name ?: 'مهمان') }}</td>
                    <td class="px-5 py-3 text-end"><x-button href="{{ route('admin.product-reviews.show', $review->id) }}" variant="light" size="sm" icon="ki-filled ki-eye" aria-label="بررسی نظر {{ $review->id }}" /></td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-10 text-center text-gray-400">نظر جدیدی در صف تأیید نیست.</td></tr>
            @endforelse
        </x-datatable>
    </div>

    <aside class="admin-dash__tips" aria-label="راهنمای سریع مدیریت">
        <div>
            <h3>مدیریت سبک‌تر</h3>
            <p>آمار جمع کل و مجموع مبلغ روی کل جدول‌ها اینجا نیست؛ برای بررسی جزئی به همان بخش مربوطه بروید.</p>
        </div>
        <nav>
            <a href="{{ route('admin.discounts.index') }}"><i class="ki-filled ki-discount" aria-hidden="true"></i> کد تخفیف</a>
            <a href="{{ route('admin.banners.index') }}"><i class="ki-filled ki-picture" aria-hidden="true"></i> بنر فروشگاه</a>
            <a href="{{ route('admin.sliders.index') }}"><i class="ki-filled ki-slider-horizontal-2" aria-hidden="true"></i> اسلایدر</a>
            <a href="{{ route('admin.help.index') }}"><i class="ki-filled ki-information-2" aria-hidden="true"></i> راهنمای پنل</a>
        </nav>
    </aside>
</div>
