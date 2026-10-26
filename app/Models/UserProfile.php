<?php

namespace App\Models;

use App\DTO\user\UserProfileUpdateData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $bank_card
 * @property string|null $job
 * @property string|null $city_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class UserProfile extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function updateForUser(User $user, UserProfileUpdateData $data): ?self
    {
        $attributes = $data->toModelAttributes();

        if ($attributes === []) {
            return null;
        }

        $profile = self::query()->where('user_id', $user->id)->first();

        if ($profile === null && ! array_filter($attributes, static fn (?string $value): bool => $value !== null)) {
            return null;
        }

        if ($profile === null) {
            $profile = new self();
            $profile->user_id = $user->id;
        }

        foreach ($attributes as $attribute => $value) {
            $profile->setAttribute($attribute, $value);
        }

        $profile->save();

        return $profile;
    }
}
