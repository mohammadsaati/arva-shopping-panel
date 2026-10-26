<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TelegramWebhookRequest extends FormRequest
{
    public function authorize(): bool
    {
        $expected = (string) config('services.telegram.webhook_secret');
        $received = $this->header('X-Telegram-Bot-Api-Secret-Token');

        if ($expected === '') {
            return app()->environment('local');
        }

        return is_string($received) && hash_equals($expected, $received);
    }

    public function rules(): array
    {
        return [
            'update_id' => ['sometimes', 'integer'],
            'message' => ['sometimes', 'array'],
            'message.text' => ['sometimes', 'string'],
            'message.chat' => ['sometimes', 'array'],
            'message.chat.id' => ['sometimes', 'integer'],
            'message.chat.type' => ['sometimes', 'string'],
        ];
    }
}
