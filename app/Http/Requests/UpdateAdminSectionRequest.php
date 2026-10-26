<?php

namespace App\Http\Requests;

use App\Enums\GlobalStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sectionId = (int) $this->route('sectionId');

        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'alpha_dash', 'max:255', Rule::unique('sections', 'slug')->ignore($sectionId)],
            'bg_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'bg_image' => ['nullable', 'image', 'max:5120'],
            'remove_bg_image' => ['nullable', 'boolean'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['image', 'max:5120'],
            'delete_image_ids' => ['nullable', 'array', 'max:10'],
            'delete_image_ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists('section_images', 'id')->where('section_id', $sectionId),
            ],
            'item_ids' => ['nullable', 'array', 'max:100'],
            'item_ids.*' => ['integer', 'distinct', 'exists:items,id'],
            'status' => ['required', 'string', Rule::enum(GlobalStatusEnum::class)],
        ];
    }
}
