<?php

namespace App\Services\Contracts;

use App\DTO\Vendor\VendorCreateData;
use App\Models\User;
use App\Models\Vendor;

interface VendorServiceInterface
{
    /** @return array<string, array<int, string>> */
    public function cityOptions(): array;

    public function createForUser(User $user, VendorCreateData $data): Vendor;

    public function updateForUser(Vendor $vendor, VendorCreateData $data): Vendor;

    public function details(int $vendorId): Vendor;

    public function profileForUser(User $user): Vendor;
}
