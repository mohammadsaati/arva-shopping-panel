<x-user-layout title="صفحه پیدا نشد" :chrome="false">
    <x-auth.stage
        mode="login"
        title="صفحه‌ای که دنبال آن بودید پیدا نشد"
        subtitle="ممکن است آدرس را اشتباه وارد کرده باشید یا این صفحه حذف شده باشد."
        welcome-title="خطای ۴۰۴"
        welcome-text="می‌توانید به صفحه اصلی برگردید یا از منوی فروشگاه محصولات را جستجو کنید."
    >
        <div class="user-auth-alert" role="alert">آدرس درخواستی در فروشگاه وجود ندارد.</div>
        <div class="flex flex-wrap gap-3">
            <x-button :href="route('home')" type="primary" icon="ki-filled ki-home-2">صفحه اصلی</x-button>
            <x-button :href="route('cart.index')" variant="light" icon="ki-filled ki-handcart">سبد خرید</x-button>
        </div>
    </x-auth.stage>
</x-user-layout>
