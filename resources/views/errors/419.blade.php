<x-user-layout title="نشست منقضی شد" :chrome="false">
    <x-auth.stage
        mode="login"
        title="نشست شما منقضی شده است"
        subtitle="برای امنیت، فرم‌ها پس از مدتی بی‌فعالیتی بسته می‌شوند."
        welcome-title="خطای ۴۱۹"
        welcome-text="صفحه را یک‌بار دیگر بارگذاری کنید و در صورت نیاز دوباره وارد شوید."
    >
        <div class="user-auth-alert" role="alert">لطفاً صفحه را تازه‌سازی کنید و عملیات را دوباره انجام دهید.</div>
        <div class="flex flex-wrap gap-3">
            <x-button :href="url()->previous() !== url()->current() ? url()->previous() : route('home')" type="primary" icon="ki-filled ki-arrows-circle">تلاش دوباره</x-button>
            <x-button :href="route('home')" variant="light">صفحه اصلی</x-button>
        </div>
    </x-auth.stage>
</x-user-layout>
