<?php

namespace App\Http\Requests;

use App\Enums\GlobalStatusEnum;
use App\Http\Requests\Concerns\ReadsSpecificationAnswers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminItemRequest extends FormRequest
{
    use ReadsSpecificationAnswers;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $itemId = (int) $this->route('itemId');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'alpha_dash', 'max:255', Rule::unique('items', 'slug')->ignore($itemId)],
            'code' => ['required', 'integer', 'min:1', 'max:4294967295', Rule::unique('items', 'code')->ignore($itemId)],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'image' => ['nullable', 'image', 'max:5120'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::enum(GlobalStatusEnum::class)],
            'is_amazing' => ['nullable', 'boolean'],
            'amazing_end_time' => ['nullable', 'jdatetime:Y/m/d H:i:s'],
            'specifications_present' => ['nullable', 'boolean'],
            'specifications' => ['nullable', 'array'],
            'specifications.*' => ['array:text,number,boolean,option,options'],
            'specifications.*.text' => ['nullable', 'string', 'max:2000'],
            'specifications.*.number' => ['nullable', 'numeric', 'between:-999999999999.999,999999999999.999'],
            'specifications.*.boolean' => ['nullable', 'boolean'],
            'specifications.*.option' => ['nullable', 'integer'],
            'specifications.*.options' => ['nullable', 'array'],
            'specifications.*.options.*' => ['integer', 'distinct'],
        ];
    }
}
