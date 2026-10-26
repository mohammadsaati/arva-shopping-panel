<?php

namespace App\Models;

use App\DTO\Payment\PaymentGatewayFilterData;
use App\DTO\Payment\PaymentGatewaySettings;
use App\DTO\Payment\PaymentGatewayUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Enums\PaymentCurrencyEnum;
use App\Enums\PaymentGatewayDriverEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property PaymentGatewayDriverEnum $driver
 * @property string $name
 * @property string|null $image
 * @property GlobalStatusEnum $status
 * @property PaymentGatewaySettings $settings
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class PaymentGateway extends Model
{
    /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\PaymentGatewayFactory> */
    use HasFactory;

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    protected function casts(): array
    {
        return [
            'driver' => PaymentGatewayDriverEnum::class,
            'status' => GlobalStatusEnum::class,
            'settings' => PaymentGatewaySettings::class,
        ];
    }

    public static function filter(PaymentGatewayFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name', 'driver', 'status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'asc'];

        $query = self::query()
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('driver', 'like', "%{$search}%");
                });
            })
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('status', $filters->status->value))
            ->when($filters->driver !== null, fn (Builder $builder) => $builder->where('driver', $filters->driver->value));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    public static function details(int $gatewayId): self
    {
        return self::query()->findOrFail($gatewayId);
    }

    /** @return Collection<int, self> */
    public static function activeForCheckout(): Collection
    {
        return self::query()
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->orderBy('name')
            ->orderBy('id')
            ->get();
    }

    public static function findActive(int $gatewayId): ?self
    {
        return self::query()
            ->whereKey($gatewayId)
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->first();
    }

    public function updateDetails(PaymentGatewayUpdateData $data, ?string $imagePath = null): self
    {
        $attributes = $data->toModelAttributes();
        $nextSettings = $data->appliedSettings($this->settings);

        foreach ($attributes as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        if ($imagePath !== null) {
            $this->image = $imagePath;
        } elseif ($data->removeImage) {
            $this->image = null;
        }

        $this->settings = $nextSettings;
        $this->save();

        return $this;
    }

    public function toggleStatus(): self
    {
        $this->status = $this->status === GlobalStatusEnum::ACTIVE
            ? GlobalStatusEnum::INACTIVE
            : GlobalStatusEnum::ACTIVE;
        $this->save();

        return $this;
    }

    public function merchantId(): string
    {
        return $this->settings->merchantId;
    }

    public function usesSandbox(): bool
    {
        return $this->settings->sandbox;
    }

    public function currency(): PaymentCurrencyEnum
    {
        return $this->settings->currency;
    }

    public function displayImage(): string
    {
        return $this->image !== null && $this->image !== ''
            ? $this->image
            : $this->driver->defaultImage();
    }
}
