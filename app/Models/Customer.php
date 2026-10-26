<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $city_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 * @property-read City|null $city
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CustomerAddress> $addresses
 */
#[Guarded('id')]
class Customer extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public static function forUser(User $user): self
    {
        $customer = self::query()->where('user_id', $user->id)->first();

        if ($customer !== null) {
            return $customer;
        }

        $customer = new self();
        $customer->user_id = $user->id;
        $customer->save();

        return $customer;
    }
}
