@props(['cityOptions' => [], 'rates' => []])

@php
    $rows = old('city_rates', old('city_rates_present') ? [] : $rates);
    $rows = is_array($rows) ? $rows : [];
    $nextIndex = $rows === [] ? 0 : max(array_map('intval', array_keys($rows))) + 1;
@endphp

<section data-product-city-rates data-next-index="{{ $nextIndex }}" class="border-t border-gray-200 pt-6 dark:border-gray-700" aria-labelledby="product-city-rates-title">
    <input type="hidden" name="city_rates_present" value="1">
    <h2 id="product-city-rates-title" class="text-lg font-semibold">هزینه ارسال شهرها</h2>
    <p class="mt-1 text-sm text-gray-500">مبلغ بالا نرخ پیش‌فرض است. برای شهرهای استثنا نرخ جدا ثبت کنید؛ صفر یعنی ارسال رایگان به آن شهر.</p>

    @error('city_rates') <p class="mt-2 text-xs text-danger" role="alert">{{ $message }}</p> @enderror
    @error('city_rates.*.city_id') <p class="mt-2 text-xs text-danger" role="alert">{{ $message }}</p> @enderror
    @error('city_rates.*.shipping_price') <p class="mt-2 text-xs text-danger" role="alert">{{ $message }}</p> @enderror

    <div data-city-rate-list class="mt-4 grid gap-3">
        @foreach ($rows as $index => $rate)
            <div data-city-rate-row class="grid items-end gap-3 rounded-lg border border-gray-200 p-3 dark:border-gray-700 sm:grid-cols-[minmax(0,1fr)_minmax(0,12rem)_auto]">
                <x-form.select name="city_rates[{{ $index }}][city_id]" label="شهر" :options="$cityOptions" :selected="data_get($rate, 'city_id')" placeholder="انتخاب شهر" required />
                <x-form.input name="city_rates[{{ $index }}][shipping_price]" label="هزینه ارسال (تومان)" type="number" min="0" dir="ltr" :value="data_get($rate, 'shipping_price')" required />
                <x-button type="button" variant="light" size="sm" data-remove-city-rate aria-label="حذف نرخ این شهر">حذف</x-button>
            </div>
        @endforeach
    </div>

    <template data-city-rate-template>
        <div data-city-rate-row class="grid items-end gap-3 rounded-lg border border-gray-200 p-3 dark:border-gray-700 sm:grid-cols-[minmax(0,1fr)_minmax(0,12rem)_auto]">
            <x-form.select name="city_rates[__index__][city_id]" label="شهر" :options="$cityOptions" placeholder="انتخاب شهر" required />
            <x-form.input name="city_rates[__index__][shipping_price]" label="هزینه ارسال (تومان)" type="number" min="0" dir="ltr" value="0" required />
            <x-button type="button" variant="light" size="sm" data-remove-city-rate aria-label="حذف نرخ این شهر">حذف</x-button>
        </div>
    </template>

    <x-button type="button" variant="light" icon="ki-filled ki-plus" class="mt-4" data-add-city-rate>افزودن نرخ شهر</x-button>
</section>
