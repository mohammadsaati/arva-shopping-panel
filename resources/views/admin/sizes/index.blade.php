<x-panel-layout
    title="سایزها"
    sub-title="مدیریت سایزهایی که برای تنوع محصولات انتخاب می‌شوند."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <livewire:admin.sizes.size-table />
</x-panel-layout>
