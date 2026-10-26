<div>
    <x-admin.filter-panel :active-count="$this->activeFilterCount">
        <x-admin.filter-search label="جستجو در نظرها" placeholder="متن، کالا یا نام کاربر..." />
        <x-admin.filter-select label="وضعیت تأیید" empty-label="همه وضعیت‌ها" wire:model.live="status">
            @foreach ($statusOptions as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
        </x-admin.filter-select>
    </x-admin.filter-panel>

    <x-datatable title="نظرهای ثبت‌شده" description="فقط نظرهای تأییدشده در صفحه محصول نمایش داده می‌شوند.">
        <x-slot:head>
            <x-datatable-heading sortable sort="id" :sort-by="$sortBy" :sort-dir="$sortDir" class="w-16">شناسه</x-datatable-heading>
            <x-datatable-heading>نظر</x-datatable-heading>
            <x-datatable-heading>محصول</x-datatable-heading>
            <x-datatable-heading>ثبت‌کننده</x-datatable-heading>
            <x-datatable-heading sortable sort="rating" :sort-by="$sortBy" :sort-dir="$sortDir">امتیاز</x-datatable-heading>
            <x-datatable-heading sortable sort="approval_status" :sort-by="$sortBy" :sort-dir="$sortDir">وضعیت</x-datatable-heading>
            <x-datatable-heading sortable sort="created_at" :sort-by="$sortBy" :sort-dir="$sortDir">تاریخ</x-datatable-heading>
            <x-datatable-heading class="w-24 text-end">عملیات</x-datatable-heading>
        </x-slot:head>

        @forelse($reviews as $review)
            @php
                $reviewerName = $review->user
                    ? trim($review->user->first_name . ' ' . $review->user->last_name)
                    : $review->guest_name;
            @endphp
            <tr class="border-b border-gray-100 transition-colors hover:bg-primary/5">
                <td class="px-5 py-3 font-mono text-gray-400">#{{ $review->id }}</td>
                <td class="max-w-sm px-5 py-3">
                    <a href="{{ route('admin.product-reviews.show', $review->id) }}" class="font-medium text-primary hover:underline">
                        {{ $review->title ?: \Illuminate\Support\Str::limit($review->body, 55) }}
                    </a>
                    <span class="mt-0.5 block text-xs text-gray-400">{{ \Illuminate\Support\Str::limit($review->body, 90) }}</span>
                </td>
                <td class="px-5 py-3">
                    <a href="{{ route('admin.items.show', $review->item_id) }}" class="font-medium text-gray-700 hover:text-primary">{{ $review->item->name }}</a>
                    <span class="mt-0.5 block text-xs text-gray-400">کد کالا {{ $review->item->code }}</span>
                </td>
                <td class="px-5 py-3 text-gray-600">
                    {{ $reviewerName ?: 'مهمان' }}
                    @if($review->verified_purchase_at)
                        <span class="mt-1 block text-xs text-success">خریدار تأییدشده</span>
                    @endif
                </td>
                <td class="px-5 py-3 font-semibold text-warning">{{ $review->rating }} / ۵</td>
                <td class="px-5 py-3">
                    <x-badge :variant="$review->approval_status->color()" size="sm" dot>{{ $review->approval_status->trans() }}</x-badge>
                </td>
                <td class="px-5 py-3 text-gray-500">{{ jalali_date($review->created_at) }}</td>
                <td class="px-5 py-3 text-end">
                    <x-table.actions :label="'عملیات نظر ' . $review->id">
                        <div class="menu-item">
                            <a href="{{ route('admin.product-reviews.show', $review->id) }}" class="menu-link" role="menuitem" data-dropdown-dismiss="true">
                                <span class="menu-icon"><i class="ki-filled ki-notepad-edit" aria-hidden="true"></i></span>
                                <span class="menu-title">بررسی و ویرایش</span>
                            </a>
                        </div>
                    </x-table.actions>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="px-5 py-12 text-center text-gray-400">نظری با این مشخصات پیدا نشد.</td></tr>
        @endforelse
    </x-datatable>

    <x-pagination-bar :paginator="$reviews" :per-page="$perPage" :per-page-options="$perPageOptions" />
</div>
