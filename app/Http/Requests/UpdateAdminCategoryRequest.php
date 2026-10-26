<?php

namespace App\Http\Requests;

use App\Enums\GlobalStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = (int) $this->route('categoryId');

        return [
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id')->where('status', GlobalStatusEnum::ACTIVE->value),
                Rule::notIn([$categoryId]),
            ],
            'slug' => ['required', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($categoryId)],
            'priority' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'icon' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'max:5120'],
            'status' => ['required', 'string', Rule::enum(GlobalStatusEnum::class)],
        ];
    }
}
