<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class StoreProductReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $guestRules = $this->user('web') === null ? ['required'] : ['nullable'];

        return [
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:150'],
            'body' => ['required', 'string', 'min:10', 'max:5000'],
            'guest_name' => [...$guestRules, 'string', 'max:100'],
            'guest_email' => [...$guestRules, 'email', 'max:190'],
            'positive_points' => ['sometimes', 'array', 'max:10'],
            'positive_points.*' => ['nullable', 'string', 'max:255'],
            'negative_points' => ['sometimes', 'array', 'max:10'],
            'negative_points.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'لطفاً امتیاز محصول را انتخاب کنید.',
            'rating.between' => 'امتیاز باید بین ۱ تا ۵ باشد.',
            'title.max' => 'عنوان نظر نمی‌تواند بیشتر از ۱۵۰ نویسه باشد.',
            'body.required' => 'متن نظر را بنویسید.',
            'body.min' => 'متن نظر باید دست‌کم ۱۰ نویسه باشد.',
            'body.max' => 'متن نظر نمی‌تواند بیشتر از ۵۰۰۰ نویسه باشد.',
            'guest_name.required' => 'برای ثبت نظر نام نمایشی خود را وارد کنید.',
            'guest_email.required' => 'برای ثبت نظر ایمیل خود را وارد کنید.',
            'guest_email.email' => 'ایمیل واردشده معتبر نیست.',
            'positive_points.max' => 'حداکثر ۱۰ نکته مثبت می‌توانید ثبت کنید.',
            'positive_points.*.max' => 'هر نکته مثبت باید حداکثر ۲۵۵ نویسه باشد.',
            'negative_points.max' => 'حداکثر ۱۰ نکته منفی می‌توانید ثبت کنید.',
            'negative_points.*.max' => 'هر نکته منفی باید حداکثر ۲۵۵ نویسه باشد.',
        ];
    }

    public function reviewTitle(): ?string
    {
        $title = trim($this->string('title')->value());

        return $title !== '' ? $title : null;
    }

    public function positivePoints(): array
    {
        return $this->cleanPoints('positive_points');
    }

    public function negativePoints(): array
    {
        return $this->cleanPoints('negative_points');
    }

    public function guestName(): ?string
    {
        $name = trim($this->string('guest_name')->value());

        return $name !== '' ? $name : null;
    }

    public function guestEmail(): ?string
    {
        $email = trim($this->string('guest_email')->value());

        return $email !== '' ? $email : null;
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
