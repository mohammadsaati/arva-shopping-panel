<x-panel-layout title="داشبورد فروشنده" sub-title="آیتم‌ها، محصولات و سفارش‌های فروشگاه خود را مدیریت کنید.">
    <div class="grid gap-5 sm:grid-cols-3">
        <x-card title="آیتم‌های من">
            <p class="text-3xl font-semibold">{{ number_format($counts['items']) }}</p>
            <div class="mt-4"><x-button href="{{ route('vendor.items.index') }}" variant="light">مشاهده آیتم‌ها</x-button></div>
        </x-card>
        <x-card title="محصولات من">
            <p class="text-3xl font-semibold">{{ number_format($counts['products']) }}</p>
            <div class="mt-4"><x-button href="{{ route('vendor.products.index') }}" variant="light">مشاهده محصولات</x-button></div>
        </x-card>
        <x-card title="سفارش‌های من">
            <p class="text-3xl font-semibold">{{ number_format($counts['orders']) }}</p>
            <div class="mt-4"><x-button href="{{ route('vendor.orders.index') }}" variant="light">مشاهده سفارش‌ها</x-button></div>
        </x-card>
    </div>
</x-panel-layout>
