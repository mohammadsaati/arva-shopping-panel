<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShoppingCartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items' => ['sometimes', 'array'],
            'items.*' => ['required', 'integer', 'min:1', 'max:999999'],
        ];
    }

    /** @return array<int|string, int> */
    public function quantities(): array
    {
        return array_map(
            static fn (mixed $quantity): int => (int) $quantity,
            $this->validated('items', []),
        );
    }
}
