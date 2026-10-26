<x-panel-layout title="اعضای خبرنامه" sub-title="ایمیل‌های ثبت‌شده و وضعیت عضویت را مدیریت کنید.">
    @if (session('success'))<x-alert type="success">{{ session('success') }}</x-alert>@endif
    <div><a class="btn btn-light" href="{{ route('admin.settings.edit') }}">بازگشت به تنظیمات سایت</a></div>
    <livewire:admin.newsletter-subscriber-table />
</x-panel-layout>
