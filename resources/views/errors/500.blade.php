<x-user-layout title="خطای سرور" :chrome="false">
    <x-auth.stage
        mode="login"
        title="مشکلی در سرور پیش آمد"
        subtitle="در حال بررسی هستیم. لطفاً چند دقیقه بعد دوباره امتحان کنید."
        welcome-title="خطای ۵۰۰"
        welcome-text="اگر خرید یا پرداخت ناتمام مانده، قبل از تلاش مجدد وضعیت سفارش را در حساب کاربری بررسی کنید."
    >
        <div class="user-auth-alert" role="alert">در حال حاضر امکان نمایش این صفحه وجود ندارد.</div>
        <x-button :href="route('home')" type="primary" icon="ki-filled ki-home-2">بازگشت به فروشگاه</x-button>
    </x-auth.stage>
</x-user-layout>
