<?php

namespace App\Http\Requests;

use App\Enums\SpecificationTypeEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveSpecificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id' => ['required', 'integer', 'exists:specification_groups,id'],
            'key' => ['required', 'string', 'alpha_dash', 'max:255', Rule::unique('specifications', 'key')
                ->where('specification_group_id', (int) $this->input('group_id'))
                ->ignore((int) $this->route('specificationId'))],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::enum(SpecificationTypeEnum::class)],
            'unit' => ['nullable', 'string', 'max:255'],
            'is_required' => ['nullable', 'boolean'],
            'is_highlighted' => ['nullable', 'boolean'],
            'is_filterable' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'options' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
