<x-panel-layout
    title="شهرها"
    sub-title="مدیریت شهرها و اتصال هر شهر به استان مربوطه."
>
    @if (session('success'))
        <x-alert type="success" class="mb-5">{{ session('success') }}</x-alert>
    @endif

    <livewire:admin.cities.city-table />
</x-panel-layout>
