<x-panel-layout :title="$kinds[$kind]" sub-title="آیتم‌های فوتر را ویرایش و مرتب کنید.">
    @if (session('success'))<x-alert type="success">{{ session('success') }}</x-alert>@endif
    @if ($errors->any())<x-alert type="danger">{{ $errors->first() }}</x-alert>@endif

    <div class="flex flex-wrap gap-2">
        <a class="btn btn-light" href="{{ route('admin.settings.edit') }}">تنظیمات سایت</a>
        @foreach ($kinds as $key => $title)
            <a href="{{ route('admin.footer.index', $key) }}" @class(['btn', 'btn-primary' => $kind === $key, 'btn-light' => $kind !== $key])>{{ $title }}</a>
        @endforeach
    </div>

    <livewire:admin.footer-content-table :kind="$kind" />
</x-panel-layout>
