<?php

namespace App\Models;

use App\DTO\user\UserCreateData;
use App\DTO\user\UserAdminUpdateData;
use App\DTO\user\UserFilterData;
use App\DTO\user\UserUpdateData;
use App\DTO\Discount\DiscountTargetFilterData;
use App\Enums\GlobalStatusEnum;
use App\Enums\UserTypeEnum;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use RuntimeException;

/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property GlobalStatusEnum $status
 * @property UserTypeEnum $type
 * @property string $api_key
 * @property string $phone
 * @property string|null $recovery_email
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read UserProfile|null $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Order> $orders
 * @property-read \Illuminate\Database\Eloquent\Collection<int, ProductReview> $productReviews
 * @property-read Customer|null $customer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CustomerAddress> $addresses
 */
#[Guarded('id')]
#[Hidden('password', 'api_key', 'remember_token')]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function addresses(): HasManyThrough
    {
        return $this->hasManyThrough(CustomerAddress::class, Customer::class, 'user_id', 'customer_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => GlobalStatusEnum::class,
            'type' => UserTypeEnum::class,
            'password' => 'hashed',
        ];
    }

    public static function filter(UserFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'first_name', 'phone', 'type', 'status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;

                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($filters->type !== null, fn (Builder $builder) => $builder->where('type', $filters->type))
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('status', $filters->status));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    public static function filterForDiscountSelection(DiscountTargetFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'first_name', 'last_name', 'phone', 'status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->when($filters->selectedOnly, fn (Builder $builder) => $builder->whereKey($filters->selectedIds))
            ->when($filters->search !== null && trim($filters->search) !== '', function (Builder $builder) use ($filters): void {
                $search = trim($filters->search);
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");

                    if (ctype_digit($search)) {
                        $builder->orWhereKey((int) $search);
                    }
                });
            });

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->simplePaginate(perPage: $filters->perPage, page: $filters->page)->withQueryString();
    }

    public static function existsForDiscountSelection(int $userId): bool
    {
        return self::query()->whereKey($userId)->exists();
    }

    public static function details(int $userId): self
    {
        return self::query()
            ->with('profile')
            ->findOrFail($userId);
    }

    public static function generateUniqueApiKey(): string
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $apiKey = Str::random(128);

            if (! self::query()->where('api_key', $apiKey)->exists()) {
                return $apiKey;
            }
        }

        throw new RuntimeException('Unable to generate a unique user API key after 5 attempts.');
    }

    public static function create(UserCreateData $data, string $apiKey): self
    {
        $user = new self();
        $user->first_name = $data->firstName;
        $user->last_name = $data->lastName;
        $user->phone = $data->phone;
        $user->password = $data->password;
        $user->api_key = $apiKey;
        $user->type = $data->type;
        $user->save();

        return $user;
    }

    public function updateProfile(UserUpdateData $data, ?UserProfileUpdateData $profileData = null): self
    {
        return DB::transaction(function () use ($data, $profileData): self {
            $attributes = $data->toModelAttributes();

            if ($attributes !== []) {
                foreach ($attributes as $attribute => $value) {
                    $this->setAttribute($attribute, $value);
                }

                $this->save();
            }

            if ($profileData !== null) {
                UserProfile::updateForUser($this, $profileData);
            }

            return self::details($this->id);
        });
    }

    public function updateAdminDetails(UserAdminUpdateData $data): self
    {
        return DB::transaction(function () use ($data): self {
            $attributes = $data->toModelAttributes();

            if ($attributes !== []) {
                foreach ($attributes as $attribute => $value) {
                    $this->setAttribute($attribute, $value);
                }

                $this->save();
            }

            if ($data->profile !== null) {
                UserProfile::updateForUser($this, $data->profile);
            }

            return $this;
        });
    }

    public function isPhoneTaken(string $phone): bool
    {
        return self::query()
            ->where('phone', $phone)
            ->whereKeyNot($this->getKey())
            ->exists();
    }

    public static function phoneExists(string $phone): bool
    {
        return self::query()->where('phone', $phone)->exists();
    }

    public static function recoveryEmailTaken(string $email, ?int $ignoreUserId = null): bool
    {
        $normalized = mb_strtolower(trim($email));

        if ($normalized === '') {
            return false;
        }

        return self::query()
            ->whereRaw('LOWER(recovery_email) = ?', [$normalized])
            ->when($ignoreUserId !== null, fn (Builder $builder) => $builder->whereKeyNot($ignoreUserId))
            ->exists();
    }

    public static function findActiveCustomerByPhone(string $phone): ?self
    {
        return self::query()
            ->where('phone', $phone)
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->where('type', UserTypeEnum::NORMAL->value)
            ->first();
    }

    public function updatePassword(string $password): void
    {
        $this->password = $password;
        $this->save();
    }

    public function routeNotificationForMail(): ?string
    {
        $email = trim((string) $this->recovery_email);

        return $email !== '' ? $email : null;
    }

    public function issuePasswordResetToken(): string
    {
        $plainToken = Str::random(64);

        DB::table(config('auth.passwords.users.table', 'password_reset_tokens'))->updateOrInsert(
            ['email' => $this->phone],
            [
                'token' => Hash::make($plainToken),
                'created_at' => now(),
            ],
        );

        return $plainToken;
    }

    public static function resetPasswordFromToken(string $phone, string $plainToken, string $password): void
    {
        $user = self::findActiveCustomerByPhone($phone);

        if ($user === null) {
            throw ValidationException::withMessages([
                'phone' => 'حساب کاربری با این شماره پیدا نشد.',
            ]);
        }

        $record = DB::table(config('auth.passwords.users.table', 'password_reset_tokens'))
            ->where('email', $phone)
            ->first();

        if ($record === null || ! Hash::check($plainToken, (string) $record->token)) {
            throw ValidationException::withMessages([
                'token' => 'لینک بازیابی نامعتبر یا منقضی شده است. دوباره درخواست دهید.',
            ]);
        }

        $expiresAt = now()->subMinutes((int) config('auth.passwords.users.expire', 60));

        if ($record->created_at !== null && $expiresAt->greaterThan($record->created_at)) {
            throw ValidationException::withMessages([
                'token' => 'لینک بازیابی منقضی شده است. دوباره درخواست دهید.',
            ]);
        }

        $user->updatePassword($password);

        DB::table(config('auth.passwords.users.table', 'password_reset_tokens'))
            ->where('email', $phone)
            ->delete();
    }
}
