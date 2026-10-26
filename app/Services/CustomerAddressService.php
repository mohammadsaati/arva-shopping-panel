<?php

namespace App\Services;

use App\DTO\CustomerAddress\CustomerAddressCreateData;
use App\DTO\CustomerAddress\CustomerAddressFilterData;
use App\DTO\CustomerAddress\CustomerAddressUpdateData;
use App\Models\CustomerAddress;
use App\Models\Province;
use App\Models\User;
use App\Services\Contracts\CustomerAddressServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

class CustomerAddressService implements CustomerAddressServiceInterface
{
    public function filter(User $user, CustomerAddressFilterData $filters): Paginator
    {
        return CustomerAddress::forUser($user, $filters);
    }

    /** @return Collection<int, CustomerAddress> */
    public function checkoutOptions(User $user): Collection
    {
        return CustomerAddress::checkoutOptionsForUser($user);
    }

    /** @return array{provinceOptions: array<int, string>, cityOptions: array<string, array<int, string>>} */
    public function formOptions(): array
    {
        $provinces = Province::activeWithCities();

        return [
            'provinceOptions' => $provinces->pluck('name', 'id')->all(),
            'cityOptions' => $provinces->mapWithKeys(
                fn (Province $province): array => [
                    $province->name => $province->cities->pluck('name', 'id')->all(),
                ],
            )->all(),
        ];
    }

    public function create(User $user, CustomerAddressCreateData $data): CustomerAddress
    {
        return CustomerAddress::createForUser($user, $data);
    }

    public function details(User $user, int $addressId): CustomerAddress
    {
        return CustomerAddress::detailsForUser($user, $addressId);
    }

    public function update(User $user, int $addressId, CustomerAddressUpdateData $data): CustomerAddress
    {
        return CustomerAddress::updateForUser($user, $addressId, $data);
    }
}
