<?php

namespace App\Http\Requests;

use App\Enums\GlobalStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminBlogCategoryRequest extends FormRequest
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
            'slug' => ['required', 'string', 'max:255', 'regex:/^[\pL\pN\-_]+$/u', Rule::unique('blog_categories', 'slug')->ignore($categoryId)],
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('blog_categories', 'id')->where(fn ($query) => $query->whereNull('parent_id')->where('id', '!=', $categoryId)),
            ],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'max:5120'],
            'remove_image' => ['nullable', 'boolean'],
            'priority' => ['required', 'integer', 'min:0', 'max:4294967295'],
            'status' => ['required', 'string', Rule::enum(GlobalStatusEnum::class)],
        ];
    }
}
