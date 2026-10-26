<?php

namespace App\Http\Requests;

use App\Enums\DiscountTypeEnum;
use App\Enums\GlobalStatusEnum;
use Hekmatinasser\Verta\Verta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateAdminDiscountRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if ($this->filled('code')) {
            $this->merge(['code' => strtoupper(trim((string) $this->input('code')))]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $discountId = (int) $this->route('discountId');
        $amountRules = ['required', 'integer', 'min:1'];

        if ($this->input('type') === DiscountTypeEnum::PERCENTAGE->value) {
            $amountRules[] = 'max:100';
        }

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'alpha_dash', 'max:50', Rule::unique('discounts', 'code')->ignore($discountId)],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'type' => ['required', 'string', Rule::enum(DiscountTypeEnum::class)],
            'amount' => $amountRules,
            'min_order_amount' => ['nullable', 'integer', 'min:0'],
            'max_discount_amount' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'jdatetime:Y/m/d H:i:s'],
            'ends_at' => ['nullable', 'jdatetime:Y/m/d H:i:s'],
            'usage_limit' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'usage_limit_per_user' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'status' => ['required', 'string', Rule::enum(GlobalStatusEnum::class)],
            'product_ids' => ['nullable', 'array', 'max:1000'],
            'product_ids.*' => ['integer', 'distinct', 'exists:products,id'],
            'category_ids' => ['nullable', 'array', 'max:500'],
            'category_ids.*' => ['integer', 'distinct', 'exists:categories,id'],
            'product_ids_present' => ['required', 'boolean'],
            'category_ids_present' => ['required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator): void {
            if ($this->filled('starts_at') && $this->filled('ends_at')) {
                try {
                    $startsAt = Verta::parseFormat('Y/m/d H:i:s', $this->string('starts_at')->value())->toCarbon();
                    $endsAt = Verta::parseFormat('Y/m/d H:i:s', $this->string('ends_at')->value())->toCarbon();

                    if ($endsAt->lessThanOrEqualTo($startsAt)) {
                        $validator->errors()->add('ends_at', 'پایان اعتبار باید بعد از زمان شروع باشد.');
                    }
                } catch (\Throwable) {
                    // The jdatetime validation rule reports malformed Jalali values.
                }
            }
        });
    }

    public function startsAt(): ?\Carbon\Carbon
    {
        return $this->dateValue('starts_at');
    }

    public function endsAt(): ?\Carbon\Carbon
    {
        return $this->dateValue('ends_at');
    }

    private function dateValue(string $key): ?\Carbon\Carbon
    {
        if (! $this->filled($key)) {
            return null;
        }

        return Verta::parseFormat('Y/m/d H:i:s', $this->string($key)->value())->toCarbon();
    }
}
