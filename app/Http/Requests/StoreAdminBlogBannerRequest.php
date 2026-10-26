<?php

namespace App\Http\Requests;

use App\Enums\GlobalStatusEnum;
use Hekmatinasser\Verta\Verta;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAdminBlogBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'eyebrow' => ['nullable', 'string', 'max:120'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['required', 'image', 'max:5120'],
            'mobile_image' => ['nullable', 'image', 'max:5120'],
            'target_url' => ['nullable', 'string', 'max:2048', 'regex:/^(https:\/\/[^\s]+|\/(?!\/)[^\s]*|#[\w-]+)$/i'],
            'target_label' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
            'starts_at' => ['nullable', 'jdatetime:Y/m/d H:i:s'],
            'ends_at' => ['nullable', 'jdatetime:Y/m/d H:i:s'],
            'status' => ['required', 'string', Rule::enum(GlobalStatusEnum::class)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator): void {
            if (! $this->filled('starts_at') || ! $this->filled('ends_at')) {
                return;
            }

            try {
                $startsAt = Verta::parseFormat('Y/m/d H:i:s', $this->string('starts_at')->value())->toCarbon();
                $endsAt = Verta::parseFormat('Y/m/d H:i:s', $this->string('ends_at')->value())->toCarbon();

                if ($endsAt->lessThan($startsAt)) {
                    $validator->errors()->add('ends_at', 'پایان نمایش باید هم‌زمان یا بعد از شروع آن باشد.');
                }
            } catch (\Throwable) {
                // The jdatetime rules report malformed Jalali values.
            }
        });
    }

    public function startsAt(): ?string
    {
        return $this->dateValue('starts_at');
    }

    public function endsAt(): ?string
    {
        return $this->dateValue('ends_at');
    }

    private function dateValue(string $key): ?string
    {
        if (! $this->filled($key)) {
            return null;
        }

        return Verta::parseFormat('Y/m/d H:i:s', $this->string($key)->value())
            ->toCarbon()
            ->format('Y-m-d H:i:s');
    }
}
