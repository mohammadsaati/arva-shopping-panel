<?php

namespace App\Models;

use App\DTO\Vendor\VendorCreateData;
use App\Enums\GlobalStatusEnum;
use App\Enums\MarketplaceApprovalStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $city_id
 * @property int $vendor_type
 * @property string $company_name
 * @property string|null $store_name
 * @property string|null $slug
 * @property string|null $description
 * @property string|null $logo_path
 * @property string|null $support_phone
 * @property int|null $commission_id
 * @property GlobalStatusEnum $status
 * @property MarketplaceApprovalStatusEnum $approval_status
 * @property Carbon|null $approved_at
 * @property string|null $rejection_reason
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read User $user
 * @property-read City $city
 * @property-read VendorType $type
 * @property-read Commission|null $commission
 */
#[Guarded('id')]
class Vendor extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(VendorType::class, 'vendor_type');
    }

    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }

    protected function casts(): array
    {
        return [
            'commission_id' => 'integer',
            'status' => GlobalStatusEnum::class,
            'approval_status' => MarketplaceApprovalStatusEnum::class,
            'approved_at' => 'datetime',
        ];
    }

    public static function forUserId(int $userId): ?self
    {
        return self::query()->where('user_id', $userId)->first();
    }

    public static function details(int $vendorId): self
    {
        return self::query()->with(['user', 'city', 'commission'])->findOrFail($vendorId);
    }

    public static function createForUser(User $user, VendorCreateData $data, int $vendorTypeId): self
    {
        $vendor = new self();
        $vendor->user_id = $user->id;
        $vendor->city_id = $data->cityId;
        $vendor->vendor_type = $vendorTypeId;
        $vendor->company_name = $data->companyName;
        $vendor->commission_id = $data->commissionId;
        $vendor->status = GlobalStatusEnum::ACTIVE;
        $vendor->approval_status = MarketplaceApprovalStatusEnum::APPROVED;
        $vendor->approved_at = now();
        $vendor->save();

        return $vendor;
    }

    public function updateDetails(VendorCreateData $data): self
    {
        $this->company_name = $data->companyName;
        $this->city_id = $data->cityId;
        $this->commission_id = $data->commissionId;
        $this->save();

        return $this;
    }
}
