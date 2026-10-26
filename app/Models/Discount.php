<?php

namespace App\Models;

use App\DTO\Discount\DiscountCreateData;
use App\DTO\Discount\DiscountFilterData;
use App\DTO\Discount\DiscountUpdateData;
use App\Enums\DiscountTypeEnum;
use App\Enums\GlobalStatusEnum;
use App\Enums\PaymentStatusEnum;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property string $code
 * @property DiscountTypeEnum $type
 * @property int $amount
 * @property int|null $min_order_amount
 * @property int|null $max_discount_amount
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property int|null $usage_limit
 * @property int|null $usage_limit_per_user
 * @property int $usage_count
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Collection<int, Product> $products
 * @property-read Collection<int, Category> $categories
 * @property-read Collection<int, Order> $orders
 * @property-read User|null $user
 */
#[Guarded('id')]
class Discount extends Model
{
    use HasFactory;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'discount_products')->withTimestamps();
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'discount_categories')->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'discount_id');
    }

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'type' => DiscountTypeEnum::class,
            'amount' => 'integer',
            'min_order_amount' => 'integer',
            'max_discount_amount' => 'integer',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'usage_limit' => 'integer',
            'usage_limit_per_user' => 'integer',
            'usage_count' => 'integer',
            'status' => GlobalStatusEnum::class,
        ];
    }

    public static function runInTransaction(Closure $callback): mixed
    {
        return DB::transaction($callback);
    }

    public static function filter(DiscountFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name', 'code', 'type', 'amount', 'usage_count', 'starts_at', 'ends_at', 'status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->with('user:id,first_name,last_name,phone')
            ->withCount(['products', 'categories'])
            ->when($filters->search !== null && trim($filters->search) !== '', function (Builder $builder) use ($filters): void {
                $search = trim($filters->search);
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', '%' . strtoupper($search) . '%');

                    if (ctype_digit($search)) {
                        $builder->orWhereKey((int) $search);
                    }
                });
            })
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('status', $filters->status->value))
            ->when($filters->type !== null, fn (Builder $builder) => $builder->where('type', $filters->type->value));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query
            ->simplePaginate(perPage: $filters->perPage, page: $filters->page)
            ->withQueryString();
    }

    public static function details(int $discountId): self
    {
        return self::query()
            ->with(['user', 'products.item', 'products.color', 'products.size', 'categories'])
            ->findOrFail($discountId);
    }

    public static function findByCode(string $code, bool $lock = false): ?self
    {
        $query = self::query()
            ->where('code', strtoupper(trim($code)))
            ->with(['products.item', 'categories']);

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->first();
    }

    public static function usageCountForUser(int $discountId, int $userId): int
    {
        return Order::query()
            ->where('discount_id', $discountId)
            ->where('user_id', $userId)
            ->where('payment_status', PaymentStatusEnum::PAID->value)
            ->count();
    }

    public static function create(DiscountCreateData $data): self
    {
        return self::runInTransaction(function () use ($data): self {
            $discount = new self();
            $discount->name = $data->name;
            $discount->code = strtoupper($data->code);
            $discount->user_id = $data->userId;
            $discount->type = $data->type;
            $discount->amount = $data->amount;
            $discount->min_order_amount = $data->minOrderAmount;
            $discount->max_discount_amount = $data->maxDiscountAmount;
            $discount->starts_at = $data->startsAt;
            $discount->ends_at = $data->endsAt;
            $discount->usage_limit = $data->usageLimit;
            $discount->usage_limit_per_user = $data->usageLimitPerUser;
            $discount->status = $data->status;
            $discount->save();

            $discount->products()->sync($data->productIds);
            $discount->categories()->sync($data->categoryIds);

            return $discount;
        });
    }

    public function updateDetails(DiscountUpdateData $data): self
    {
        return self::runInTransaction(function () use ($data): self {
            foreach ($data->toModelAttributes() as $attribute => $value) {
                $this->setAttribute($attribute, $value);
            }

            $this->save();

            if ($data->productIdsProvided) {
                $this->products()->sync($data->productIds);
            }

            if ($data->categoryIdsProvided) {
                $this->categories()->sync($data->categoryIds);
            }

            return self::details($this->id);
        });
    }

    public function recordUsage(): void
    {
        $this->increment('usage_count');
    }
}
