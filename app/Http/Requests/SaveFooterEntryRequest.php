<?php

namespace App\Http\Requests;

use App\Enums\GlobalStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveFooterEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $kind = $this->route('kind');
        $isFeature = $kind === 'features';
        $isGroup = $kind === 'groups';
        $isLink = $kind === 'links';
        $isSocial = $kind === 'social';
        $isApp = $kind === 'apps';

        return [
            'title' => [$isFeature || $isGroup ? 'required' : 'nullable', 'string', 'max:255'],
            'label' => [$isLink || $isSocial || $isApp ? 'required' : 'nullable', 'string', 'max:255'],
            'url' => [$isLink || $isSocial || $isApp ? 'required' : 'nullable', 'string', 'max:2048', function (string $attribute, mixed $value, $fail): void {
                if (! is_string($value) || $value === '') return;
                if (str_starts_with($value, '//')) {
                    $fail('پیوند داخلی نمی‌تواند با دو اسلش آغاز شود.');
                }
                if (! str_starts_with($value, '/') && ! filter_var($value, FILTER_VALIDATE_URL)) {
                    $fail('پیوند باید با / آغاز شود یا یک نشانی کامل معتبر باشد.');
                }
                if (preg_match('/^https?:\/\//i', $value) !== 1 && ! str_starts_with($value, '/')) {
                    $fail('تنها پیوندهای داخلی و نشانی‌های http یا https مجاز هستند.');
                }
            }],
            'icon' => [$isFeature || $isSocial ? 'nullable' : 'prohibited', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\s_-]*$/'],
            'footer_link_group_id' => [$isLink ? 'required' : 'prohibited', 'integer', 'exists:footer_link_groups,id'],
            'opens_in_new_tab' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'status' => ['required', 'string', Rule::enum(GlobalStatusEnum::class)],
            'image' => [$isFeature || $isSocial || $isApp ? 'nullable' : 'prohibited', 'image', 'max:5120'],
            'remove_image' => [$isFeature || $isSocial || $isApp ? 'nullable' : 'prohibited', 'boolean'],
        ];
    }
}
