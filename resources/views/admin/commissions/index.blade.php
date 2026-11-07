<x-panel-layout title="کمیسیون‌ها" sub-title="عنوان و درصد کمیسیون‌ها را تعریف کنید و هنگام ویرایش فروشنده یکی را انتخاب کنید.">
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif
    <livewire:admin.commissions.commission-table />
</x-panel-layout>
