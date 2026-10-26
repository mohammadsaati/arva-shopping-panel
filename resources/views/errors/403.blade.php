<x-user-layout title="دسترسی مجاز نیست" :chrome="false">
    <x-auth.stage
        mode="login"
        title="اجازه دسترسی به این بخش را ندارید"
        subtitle="برای ادامه ممکن است لازم باشد وارد حساب کاربری شوید یا از حساب دیگری استفاده کنید."
        welcome-title="خطای ۴۰۳"
        welcome-text="اگر فکر می‌کنید این پیام اشتباه است، با پشتیبانی فروشگاه تماس بگیرید."
    >
        <div class="user-auth-alert" role="alert">{{ $exception->getMessage() !== '' && $exception->getMessage() !== 'Forbidden' ? $exception->getMessage() : 'دسترسی به این صفحه برای شما فعال نیست.' }}</div>
        <div class="flex flex-wrap gap-3">
            @guest('web')
                <x-button :href="route('login')" type="primary" icon="ki-filled ki-entrance-left">ورود</x-button>
            @endguest
            <x-button :href="route('home')" variant="light" icon="ki-filled ki-home-2">صفحه اصلی</x-button>
        </div>
    </x-auth.stage>
</x-user-layout>
