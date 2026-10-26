<?php

namespace App\Services\Contracts;

use App\DTO\CustomerAddress\CustomerAddressCreateData;
use App\DTO\CustomerAddress\CustomerAddressFilterData;
use App\DTO\CustomerAddress\CustomerAddressUpdateData;
use App\Models\CustomerAddress;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

interface CustomerAddressServiceInterface
{
    public function filter(User $user, CustomerAddressFilterData $filters): Paginator;

    /** @return Collection<int, CustomerAddress> */
    public function checkoutOptions(User $user): Collection;

    /** @return array{provinceOptions: array<int, string>, cityOptions: array<string, array<int, string>>} */
    public function formOptions(): array;

    public function create(User $user, CustomerAddressCreateData $data): CustomerAddress;

    public function details(User $user, int $addressId): CustomerAddress;

    public function update(User $user, int $addressId, CustomerAddressUpdateData $data): CustomerAddress;
}
