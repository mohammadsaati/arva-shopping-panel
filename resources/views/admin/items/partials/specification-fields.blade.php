<x-form.hidden name="specifications_present" value="1" />

@error('specifications')
    <p class="text-sm text-danger" role="alert">{{ $message }}</p>
@enderror

@forelse ($specificationGroups as $group)
    @if ($group->specifications->isNotEmpty())
        <section class="rounded-xl border border-gray-200 p-5" aria-label="{{ $group->name }}">
            <h3 class="mb-4 text-base font-semibold">{{ $group->name }}</h3>
            <div class="grid gap-5 sm:grid-cols-2">
                @foreach ($group->specifications as $specification)
                    @php
                        $values = ($item?->specificationValues ?? collect())->where('specification_id', $specification->id);
                        $firstValue = $values->first();
                        $field = 'specifications[' . $specification->id . ']';
                    @endphp

                    @switch ($specification->type)
                        @case (\App\Enums\SpecificationTypeEnum::TEXT)
                            <x-form.textarea
                                :name="$field . '[text]'"
                                :label="$specification->name"
                                :value="$firstValue?->value_text"
                                :required="$specification->is_required"
                                rows="2"
                            />
                            @break
                        @case (\App\Enums\SpecificationTypeEnum::NUMBER)
                            <x-form.input
                                :name="$field . '[number]'"
                                :label="$specification->name"
                                :value="$firstValue?->value_number"
                                type="number"
                                step="0.001"
                                :suffix="$specification->unit"
                                :required="$specification->is_required"
                            />
                            @break
                        @case (\App\Enums\SpecificationTypeEnum::BOOLEAN)
                            <x-form.select
                                :name="$field . '[boolean]'"
                                :label="$specification->name"
                                :selected="$firstValue?->value_boolean === null ? null : (int) $firstValue->value_boolean"
                                :options="['1' => 'بله', '0' => 'خیر']"
                                placeholder="انتخاب کنید"
                                :required="$specification->is_required"
                            />
                            @break
                        @case (\App\Enums\SpecificationTypeEnum::SELECT)
                            <x-form.select
                                :name="$field . '[option]'"
                                :label="$specification->name"
                                :selected="$firstValue?->specification_option_id"
                                :options="$specification->options->pluck('label', 'id')->all()"
                                placeholder="انتخاب کنید"
                                :required="$specification->is_required"
                            />
                            @break
                        @case (\App\Enums\SpecificationTypeEnum::MULTI_SELECT)
                            <x-form.select
                                :name="$field . '[options]'"
                                :label="$specification->name"
                                :selected="$values->pluck('specification_option_id')->all()"
                                :options="$specification->options->pluck('label', 'id')->all()"
                                multiple
                                :required="$specification->is_required"
                                hint="برای انتخاب چند گزینه از Ctrl یا Command استفاده کنید."
                            />
                            @break
                    @endswitch
                @endforeach
            </div>
        </section>
    @endif
@empty
    <p class="text-sm text-gray-500">
        {{ ($selectedCategoryId ?? null) ? 'برای این دسته‌بندی هنوز مشخصه‌ای تعریف نشده است.' : 'دسته‌بندی را انتخاب کنید تا فیلدهای مشخصات نمایش داده شوند.' }}
    </p>
@endforelse
