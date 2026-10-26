<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveSpecificationGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('specification_groups', 'name')
                ->where('category_id', (int) $this->route('categoryId'))
                ->ignore((int) $this->route('groupId'))],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
        ];
    }
}
