<x-user-layout :title="site_settings()?->meta_title ?: site_name().' | صفحه اصلی'">
    <div class="store-home">
        <livewire:storefront.home-catalog />
    </div>
</x-user-layout>
