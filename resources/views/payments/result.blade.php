<x-user-layout :title="$outcome === 'success' ? 'پرداخت موفق' : 'وضعیت پرداخت'" :chrome="false">
    <x-auth.stage
        mode="login"
        :title="$outcome === 'success' ? 'پرداخت تأیید شد' : 'پرداخت تکمیل نشد'"
        :subtitle="$outcome === 'success'
            ? 'برای دیدن جزئیات سفارش شماره '.$orderId.' وارد حساب خود شوید.'
            : 'اگر هنوز وارد نشده‌اید، بعد از ورود می‌توانید پرداخت را از صفحه سفارش دوباره شروع کنید.'"
        welcome-title="نتیجه پرداخت"
        welcome-text="این صفحه تا دو ساعت معتبر است. برای امنیت، جزئیات کامل فقط بعد از ورود نمایش داده می‌شود."
    >
        @if ($outcome === 'success')
            <div class="user-auth-alert user-auth-alert--success" role="status">
                پرداخت سفارش شماره {{ $orderId }} با موفقیت ثبت شد.
            </div>
        @else
            <div class="user-auth-alert" role="alert">
                {{ session('payment_error', 'پرداخت تأیید نشد یا لغو شد.') }}
            </div>
        @endif

        <div class="flex flex-wrap gap-3">
            <x-button :href="$loginUrl" type="primary" icon="ki-filled ki-arrow-left">ورود به حساب</x-button>
            <x-button :href="route('home')" variant="light">بازگشت به فروشگاه</x-button>
        </div>
    </x-auth.stage>
</x-user-layout>
