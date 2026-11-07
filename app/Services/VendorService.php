<?php

namespace App\Services;

use App\DTO\Vendor\VendorCreateData;
use App\Models\City;
use App\Models\Commission;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorType;
use App\Enums\UserTypeEnum;
use App\Enums\GlobalStatusEnum;
use App\Enums\MarketplaceApprovalStatusEnum;
use Illuminate\Auth\Access\AuthorizationException;
use App\Services\Contracts\VendorServiceInterface;
use Illuminate\Validation\ValidationException;

class VendorService implements VendorServiceInterface
{
    public function cityOptions(): array
    {
        return City::activeVendorOptions();
    }

    public function createForUser(User $user, VendorCreateData $data): Vendor
    {
        if (! City::isActiveId($data->cityId)) {
            throw ValidationException::withMessages(['vendor_city_id' => 'شهر انتخاب‌شده معتبر نیست.']);
        }

        $this->validateCommission($data->commissionId);

        return Vendor::createForUser(
            $user,
            $data,
            VendorType::ensureGeneral()->id,
        );
    }

    public function updateForUser(Vendor $vendor, VendorCreateData $data): Vendor
    {
        if (! City::isActiveId($data->cityId)) {
            throw ValidationException::withMessages(['vendor_city_id' => 'شهر انتخاب‌شده معتبر نیست.']);
        }

        $this->validateCommission($data->commissionId);

        return $vendor->updateDetails($data);
    }

    public function details(int $vendorId): Vendor
    {
        return Vendor::details($vendorId);
    }

    public function profileForUser(User $user): Vendor
    {
        $vendor = $user->type === UserTypeEnum::VENDOR ? Vendor::forUserId($user->id) : null;

        if ($user->status !== GlobalStatusEnum::ACTIVE
            || $vendor === null
            || $vendor->status !== GlobalStatusEnum::ACTIVE
            || $vendor->approval_status !== MarketplaceApprovalStatusEnum::APPROVED) {
            throw new AuthorizationException('دسترسی به پنل فروشنده مجاز نیست.');
        }

        return $vendor;
    }

    private function validateCommission(?int $commissionId): void
    {
        if ($commissionId !== null && ! Commission::existsId($commissionId)) {
            throw ValidationException::withMessages(['commission_id' => 'کمیسیون انتخاب‌شده معتبر نیست.']);
        }
    }
}
