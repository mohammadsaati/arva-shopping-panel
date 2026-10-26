<div>
    <x-admin.filter-panel>
        <x-admin.filter-search label="جستجو در ایمیل‌ها" placeholder="آدرس ایمیل..." />
        <x-admin.filter-select label="وضعیت" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="اعضای خبرنامه">
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir">شناسه</x-datatable-heading>
            <x-datatable-heading sortable sort="email" :sort-by="$sortBy" :sort-dir="$sortDir">ایمیل</x-datatable-heading>
            <x-datatable-heading sortable sort="subscribed_at" :sort-by="$sortBy" :sort-dir="$sortDir">زمان عضویت</x-datatable-heading>
            <x-datatable-heading sortable sort="status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading>عملیات</x-datatable-heading>
        </x-slot:head>
        @forelse ($subscribers as $subscriber)
            @php($isActive = $subscriber->status === \App\Enums\GlobalStatusEnum::ACTIVE)
            <tr>
                <td class="px-5 py-3">{{ $subscriber->id }}</td>
                <td class="px-5 py-3" dir="ltr">{{ $subscriber->email }}</td>
                <td class="px-5 py-3">{{ jalali_date($subscriber->subscribed_at, 'Y/m/d H:i') }}</td>
                <td class="px-5 py-3"><x-badge :variant="$subscriber->status->color()" size="sm" dot>{{ $subscriber->status->trans() }}</x-badge></td>
                <td class="px-5 py-3"><x-card.form :action="route('admin.newsletter-subscribers.update', $subscriber->id)" method="PATCH" class="inline-block">
                    <x-form.hidden name="status" :value="$isActive ? \App\Enums\GlobalStatusEnum::INACTIVE->value : \App\Enums\GlobalStatusEnum::ACTIVE->value" />
                    <button type="submit" class="btn btn-sm btn-light">{{ $isActive ? 'غیرفعال‌کردن' : 'فعال‌کردن' }}</button>
                </x-card.form></td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-5 py-10 text-center text-gray-500">هنوز ایمیلی در خبرنامه ثبت نشده است.</td></tr>
        @endforelse
    </x-datatable>
    <x-pagination-bar :paginator="$subscribers" :per-page="$perPage" :per-page-options="[10, 15, 25, 50]" />
</div>
