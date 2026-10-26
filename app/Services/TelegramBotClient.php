<?php

namespace App\Services;

use App\DTO\Telegram\TelegramWebhookRegistrationResult;
use App\Services\Contracts\TelegramBotClientInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramBotClient implements TelegramBotClientInterface
{
    public function registerWebhook(string $url, string $secret): TelegramWebhookRegistrationResult
    {
        $token = trim((string) config('services.telegram.bot_token'));

        if ($token === '') {
            return new TelegramWebhookRegistrationResult(false, 'توکن ربات تلگرام در تنظیمات سرور ثبت نشده است.');
        }

        try {
            $response = Http::asJson()
                ->connectTimeout(3)
                ->timeout(10)
                ->post("https://api.telegram.org/bot{$token}/setWebhook", [
                    'url' => $url,
                    'secret_token' => $secret,
                    'allowed_updates' => ['message'],
                ]);

            $body = $response->json();

            if (is_array($body)) {
                $description = isset($body['description']) && is_string($body['description'])
                    ? trim($body['description'])
                    : '';

                if ($response->successful() && ($body['ok'] ?? null) === true && ($body['result'] ?? null) === true) {
                    return new TelegramWebhookRegistrationResult(true, $description !== '' ? $description : 'وب‌هوک در تلگرام ثبت شد.');
                }

                $errorCode = isset($body['error_code']) && is_int($body['error_code'])
                    ? $body['error_code']
                    : ($response->failed() ? $response->status() : null);

                return new TelegramWebhookRegistrationResult(
                    false,
                    $description !== '' ? $description : 'تلگرام ثبت وب‌هوک را تأیید نکرد.',
                    $errorCode,
                );
            }

            Log::warning('Telegram webhook registration returned an invalid response.', ['status' => $response->status()]);

            return new TelegramWebhookRegistrationResult(
                false,
                'پاسخ تلگرام قابل خواندن نبود. دوباره تلاش کنید.',
                $response->failed() ? $response->status() : null,
            );
        } catch (Throwable $exception) {
            Log::warning('Telegram webhook registration failed.', ['exception' => $exception::class]);

            return new TelegramWebhookRegistrationResult(false, 'ارتباط با تلگرام برقرار نشد. اتصال اینترنت سرور را بررسی کنید.');
        }
    }

    public function sendText(string $chatId, string $text): bool
    {
        $token = trim((string) config('services.telegram.bot_token'));

        if ($token === '') {
            return false;
        }

        try {
            $response = Http::asJson()
                ->connectTimeout(3)
                ->timeout(5)
                ->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $text,
                ]);

            if ($response->successful() && $response->json('ok') === true) {
                return true;
            }

            Log::warning('Telegram rejected a bot message.', ['status' => $response->status()]);
        } catch (Throwable $exception) {
            Log::warning('Telegram bot request failed.', ['exception' => $exception::class]);
        }

        return false;
    }
}
