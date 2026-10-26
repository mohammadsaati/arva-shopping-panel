<?php

namespace App\Services;

use App\DTO\Telegram\TelegramStartData;
use App\DTO\Telegram\TelegramWebhookRegistrationResult;
use App\Models\Admin;
use App\Services\Contracts\AdminTelegramConnectionServiceInterface;
use App\Services\Contracts\TelegramBotClientInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class AdminTelegramConnectionService implements AdminTelegramConnectionServiceInterface
{
    private const int LINK_LIFETIME_MINUTES = 10;

    public function __construct(private readonly TelegramBotClientInterface $bot)
    {
    }

    /** @return array{configured: bool, connected: bool, connectedAt: Carbon|null, botUsername: string|null, webhookUrl: string|null} */
    public function overview(Admin $admin): array
    {
        return [
            'configured' => $this->isConfigured(),
            'connected' => $admin->isTelegramConnected(),
            'connectedAt' => $admin->telegram_connected_at,
            'botUsername' => $this->botUsername(),
            'webhookUrl' => $this->webhookUrl(),
        ];
    }

    public function registerWebhook(): TelegramWebhookRegistrationResult
    {
        if (! $this->isConfigured()) {
            return new TelegramWebhookRegistrationResult(false, 'ابتدا توکن، نام کاربری و رمز وب‌هوک ربات را در تنظیمات سرور ثبت کنید.');
        }

        $url = $this->webhookUrl();

        if ($url === null) {
            return new TelegramWebhookRegistrationResult(false, 'آدرس APP_URL یا TELEGRAM_WEBHOOK_BASE_URL باید دامنهٔ عمومی سایت با HTTPS باشد.');
        }

        return $this->bot->registerWebhook($url, (string) config('services.telegram.webhook_secret'));
    }

    public function createLink(Admin $admin): ?string
    {
        if (! $this->isConfigured()) {
            return null;
        }

        $this->revokePendingLink($admin->id);

        $token = Str::random(48);
        $hash = hash('sha256', $token);
        $expiresAt = now()->addMinutes(self::LINK_LIFETIME_MINUTES);

        Cache::put($this->linkCacheKey($hash), $admin->id, $expiresAt);
        Cache::put($this->adminCacheKey($admin->id), $hash, $expiresAt);

        return 'https://t.me/'.$this->botUsername().'?start='.$token;
    }

    public function completeFromStart(TelegramStartData $data): void
    {
        if ($data->chatType !== 'private' || ! ctype_digit($data->chatId)) {
            return;
        }

        if (preg_match('/^\/start ([A-Za-z0-9]{48})$/D', trim($data->text), $matches) !== 1) {
            return;
        }

        $hash = hash('sha256', $matches[1]);
        $lock = Cache::lock('telegram:admin-link-lock:'.$hash, 15);

        if (! $lock->get()) {
            return;
        }

        try {
            $this->completeLockedConnection($hash, $data->chatId);
        } finally {
            $lock->release();
        }
    }

    private function completeLockedConnection(string $hash, string $chatId): void
    {
        $adminId = Cache::get($this->linkCacheKey($hash));

        if (! is_int($adminId) || Cache::get($this->adminCacheKey($adminId)) !== $hash) {
            return;
        }

        $admin = Admin::findActiveById($adminId);

        if ($admin === null) {
            $this->revokePendingLink($adminId);
            return;
        }

        if (Admin::telegramChatBelongsToAnotherAdmin($chatId, $adminId)) {
            $this->revokePendingLink($adminId);
            $this->bot->sendText($chatId, 'این حساب تلگرام قبلاً به مدیر دیگری وصل شده است.');
            return;
        }

        $admin->connectTelegram($chatId);
        $this->revokePendingLink($adminId);
        $this->bot->sendText($chatId, 'اتصال تلگرام به پنل مدیریت فروشگاه با موفقیت انجام شد.');
    }

    public function sendTest(Admin $admin): bool
    {
        if (! $this->isConfigured() || ! $admin->isTelegramConnected()) {
            return false;
        }

        return $this->bot->sendText(
            $admin->telegram_chat_id,
            'پیام آزمایشی از پنل مدیریت فروشگاه: اتصال تلگرام شما فعال است.',
        );
    }

    public function disconnect(Admin $admin): void
    {
        $this->revokePendingLink($admin->id);

        if ($admin->isTelegramConnected()) {
            $admin->disconnectTelegram();
        }
    }

    private function isConfigured(): bool
    {
        $token = trim((string) config('services.telegram.bot_token'));
        $secret = trim((string) config('services.telegram.webhook_secret'));

        return $token !== ''
            && $this->botUsername() !== null
            && preg_match('/^[A-Za-z0-9_-]{1,256}$/D', $secret) === 1;
    }

    private function botUsername(): ?string
    {
        $username = ltrim(trim((string) config('services.telegram.bot_username')), '@');

        return preg_match('/^[A-Za-z0-9_]+$/D', $username) === 1 ? $username : null;
    }

    private function webhookUrl(): ?string
    {
        $override = trim((string) config('services.telegram.webhook_base_url'));
        $baseUrl = rtrim($override !== '' ? $override : trim((string) config('app.url')), '/');
        $parts = parse_url($baseUrl);

        if (! is_array($parts)
            || ($parts['scheme'] ?? null) !== 'https'
            || empty($parts['host'])
            || isset($parts['user'])
            || isset($parts['pass'])
            || ($parts['path'] ?? '') !== ''
            || isset($parts['query'])
            || isset($parts['fragment'])) {
            return null;
        }

        return $baseUrl.route('telegram.webhook', [], false);
    }

    private function revokePendingLink(int $adminId): void
    {
        $hash = Cache::pull($this->adminCacheKey($adminId));

        if (is_string($hash)) {
            Cache::forget($this->linkCacheKey($hash));
        }
    }

    private function linkCacheKey(string $hash): string
    {
        return 'telegram:admin-link:'.$hash;
    }

    private function adminCacheKey(int $adminId): string
    {
        return 'telegram:admin-pending:'.$adminId;
    }
}
