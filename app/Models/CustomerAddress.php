<?php

namespace App\Models;

use App\DTO\CustomerAddress\CustomerAddressCreateData;
use App\DTO\CustomerAddress\CustomerAddressFilterData;
use App\DTO\CustomerAddress\CustomerAddressUpdateData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property int $id
 * @property int $customer_id
 * @property int $province_id
 * @property int $city_id
 * @property string $full_address
 * @property string|null $phone_number
 * @property string|null $recipient_name
 * @property string|null $recipient_national_code
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Customer $customer
 * @property-read Province $province
 * @property-read City $city
 */
#[Guarded('id')]
class CustomerAddress extends Model
{
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public static function forUser(User $user, CustomerAddressFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::queryForUser($user)->with(['city', 'province']);

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    /** @return Collection<int, self> */
    public static function checkoutOptionsForUser(User $user): Collection
    {
        return self::queryForUser($user)
            ->with(['city', 'province'])
            ->orderByDesc('id')
            ->get();
    }

    public static function findForCheckout(User $user, int $addressId): ?self
    {
        return self::queryForUser($user)
            ->with(['city', 'province'])
            ->find($addressId);
    }

    public static function createForUser(User $user, CustomerAddressCreateData $data): self
    {
        $address = new self();
        $address->customer_id = Customer::forUser($user)->id;
        $address->province_id = $data->provinceId;
        $address->city_id = $data->cityId;
        $address->full_address = $data->fullAddress;
        $address->phone_number = $data->phoneNumber;
        $address->recipient_name = $data->recipientName;
        $address->recipient_national_code = $data->recipientNationalCode;
        $address->save();

        return $address->load(['city', 'province']);
    }

    public static function detailsForUser(User $user, int $addressId): self
    {
        return self::queryForUser($user)
            ->with(['city', 'province'])
            ->findOrFail($addressId);
    }

    public static function updateForUser(User $user, int $addressId, CustomerAddressUpdateData $data): self
    {
        $address = self::queryForUser($user)->findOrFail($addressId);

        foreach ($data->toModelAttributes() as $attribute => $value) {
            $address->setAttribute($attribute, $value);
        }

        $address->save();

        return $address->load(['city', 'province']);
    }

    private static function queryForUser(User $user): Builder
    {
        return self::query()->whereHas(
            'customer',
            fn (Builder $builder) => $builder->where('user_id', $user->id),
        );
    }
}
