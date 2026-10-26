<?php

namespace App\Models;

use App\Enums\GlobalStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property GlobalStatusEnum $status
 * @property int|null $city_id
 * @property string|null $phone
 * @property string|null $telegram_chat_id
 * @property Carbon|null $telegram_connected_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read City|null $city
 */
#[Guarded('id')]
#[Hidden('password')]
class Admin extends Authenticatable
{
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    protected function casts(): array
    {
        return [
            'status' => GlobalStatusEnum::class,
            'password' => 'hashed',
            'telegram_connected_at' => 'datetime',
        ];
    }

    public static function findActiveByEmail(string $email): ?self
    {
        return self::query()
            ->where('email', $email)
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->first();
    }

    public static function findActiveById(int $adminId): ?self
    {
        return self::query()
            ->whereKey($adminId)
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->first();
    }

    public static function telegramChatBelongsToAnotherAdmin(string $chatId, int $adminId): bool
    {
        return self::query()
            ->where('telegram_chat_id', $chatId)
            ->whereKeyNot($adminId)
            ->exists();
    }

    public function isTelegramConnected(): bool
    {
        return $this->telegram_chat_id !== null;
    }

    public function connectTelegram(string $chatId): void
    {
        $this->telegram_chat_id = $chatId;
        $this->telegram_connected_at = now();
        $this->save();
    }

    public function disconnectTelegram(): void
    {
        $this->telegram_chat_id = null;
        $this->telegram_connected_at = null;
        $this->save();
    }
}
