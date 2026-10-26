<?php

namespace App\Http\Requests;

use App\Enums\BannerTypeEnum;
use App\Enums\GlobalStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['nullable', 'image', 'max:5120'],
            'type' => ['required', 'string', Rule::enum(BannerTypeEnum::class)],
            'product_id' => [Rule::requiredIf($this->input('type') === BannerTypeEnum::PRODUCT->value), 'nullable', 'integer', 'exists:products,id'],
            'category_id' => [Rule::requiredIf($this->input('type') === BannerTypeEnum::CATEGORY->value), 'nullable', 'integer', 'exists:categories,id'],
            'link' => [Rule::requiredIf($this->input('type') === BannerTypeEnum::LINK->value), 'nullable', 'string', 'url', 'max:2048'],
            'status' => ['required', 'string', Rule::enum(GlobalStatusEnum::class)],
        ];
    }
}
