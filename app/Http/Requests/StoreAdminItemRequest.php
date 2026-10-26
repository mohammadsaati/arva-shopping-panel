<?php

namespace App\Http\Requests;

use App\Enums\GlobalStatusEnum;
use App\Http\Requests\Concerns\ReadsSpecificationAnswers;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAdminItemRequest extends FormRequest
{
    use ReadsSpecificationAnswers;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'alpha_dash', 'max:255', 'unique:items,slug'],
            'code' => ['required', 'integer', 'min:1', 'max:4294967295', 'unique:items,code'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'brand_id' => ['nullable', 'integer', 'exists:brands,id'],
            'image' => ['nullable', 'image', 'max:5120'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'string', Rule::enum(GlobalStatusEnum::class)],
            'is_amazing' => ['nullable', 'boolean'],
            'amazing_end_time' => ['nullable', 'jdatetime:Y/m/d H:i:s'],
            'product' => ['nullable', 'array:price,color_id,size_id,stock,shipping_price,status'],
            'product.price' => ['nullable', 'required_with:product.price,product.stock,product.shipping_price', Rule::requiredIf(fn (): bool => $this->filled('city_rates')), 'integer', 'min:0'],
            'product.color_id' => ['nullable', 'integer', 'exists:colors,id'],
            'product.size_id' => ['nullable', 'integer', 'exists:sizes,id'],
            'product.stock' => ['nullable', 'required_with:product.price,product.stock,product.shipping_price', 'integer', 'min:0'],
            'product.shipping_price' => ['nullable', 'required_with:product.price,product.stock,product.shipping_price', 'integer', 'min:0'],
            'product.status' => ['nullable', 'string', Rule::enum(GlobalStatusEnum::class)],
            'city_rates' => ['nullable', 'array', 'max:100'],
            'city_rates.*.city_id' => ['required', 'integer', 'distinct', 'exists:cities,id'],
            'city_rates.*.shipping_price' => ['required', 'integer', 'min:0'],
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
