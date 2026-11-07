<?php

namespace App\Http\Requests;

use App\Enums\MarketplaceApprovalStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminVendorProductApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin') !== null;
    }

    public function rules(): array
    {
        return [
            'approval_status' => [
                'required',
                Rule::in([
                    MarketplaceApprovalStatusEnum::APPROVED->value,
                    MarketplaceApprovalStatusEnum::REJECTED->value,
                ]),
            ],
            'rejection_reason' => ['required_if:approval_status,rejected', 'nullable', 'string', 'max:2000'],
        ];
    }

    public function approvalStatus(): MarketplaceApprovalStatusEnum
    {
        return MarketplaceApprovalStatusEnum::from($this->string('approval_status')->value());
    }

    public function rejectionReason(): ?string
    {
        if (! $this->filled('rejection_reason')) {
            return null;
        }

        $reason = trim($this->string('rejection_reason')->value());

        return $reason === '' ? null : $reason;
    }
}
