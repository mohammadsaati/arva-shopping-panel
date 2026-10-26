<?php

namespace App\Http\Requests;

use App\Enums\GlobalStatusEnum;
use App\Enums\SliderTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminSliderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'image' => ['nullable', 'image', 'max:5120'],
            'type' => ['required', 'string', Rule::enum(SliderTypeEnum::class)],
            'product_id' => [Rule::requiredIf($this->input('type') === SliderTypeEnum::PRODUCT->value), 'nullable', 'integer', 'exists:products,id'],
            'category_id' => [Rule::requiredIf($this->input('type') === SliderTypeEnum::CATEGORY->value), 'nullable', 'integer', 'exists:categories,id'],
            'section_id' => [Rule::requiredIf($this->input('type') === SliderTypeEnum::SESSION->value), 'nullable', 'integer', 'exists:sections,id'],
            'link' => [Rule::requiredIf($this->input('type') === SliderTypeEnum::LINK->value), 'nullable', 'string', 'url', 'max:2048'],
            'status' => ['required', 'string', Rule::enum(GlobalStatusEnum::class)],
        ];
    }
}
