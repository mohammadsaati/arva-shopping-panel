<?php

namespace App\Http\Requests;

use App\Enums\ProductReviewApprovalStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminProductReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'approval_status' => ['required', Rule::enum(ProductReviewApprovalStatusEnum::class)],
            'positive_points' => ['sometimes', 'array', 'max:10'],
            'positive_points.*' => ['nullable', 'string', 'max:255'],
            'negative_points' => ['sometimes', 'array', 'max:10'],
            'negative_points.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    /** @return list<string> */
    public function positivePoints(): array
    {
        return $this->cleanPoints('positive_points');
    }

    /** @return list<string> */
    public function negativePoints(): array
    {
        return $this->cleanPoints('negative_points');
    }

    /** @return list<string> */
    private function cleanPoints(string $key): array
    {
        return array_values(array_filter(
            array_map(static fn (mixed $point): string => trim((string) $point), $this->array($key)),
            static fn (string $point): bool => $point !== '',
        ));
    }
}
