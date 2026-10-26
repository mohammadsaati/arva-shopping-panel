<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Discount\DiscountCreateData;
use App\DTO\Discount\DiscountUpdateData;
use App\Enums\DiscountTypeEnum;
use App\Enums\GlobalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminDiscountRequest;
use App\Http\Requests\UpdateAdminDiscountRequest;
use App\Services\Contracts\DiscountServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DiscountController extends Controller
{
    public function __construct(private readonly DiscountServiceInterface $discounts)
    {
    }

    public function index(): View
    {
        return view('admin.discounts.index');
    }

    public function create(): View
    {
        return view('admin.discounts.create', [
            'statusOptions' => $this->statusOptions(),
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function store(StoreAdminDiscountRequest $request): RedirectResponse
    {
        $discount = $this->discounts->create(new DiscountCreateData(
            name: $request->string('name')->value(),
            code: $request->string('code')->value(),
            userId: $request->filled('user_id') ? $request->integer('user_id') : null,
            type: $request->enum('type', DiscountTypeEnum::class),
            amount: $request->integer('amount'),
            minOrderAmount: $request->filled('min_order_amount') ? $request->integer('min_order_amount') : null,
            maxDiscountAmount: $request->filled('max_discount_amount') ? $request->integer('max_discount_amount') : null,
            startsAt: $request->startsAt(),
            endsAt: $request->endsAt(),
            usageLimit: $request->filled('usage_limit') ? $request->integer('usage_limit') : null,
            usageLimitPerUser: $request->filled('usage_limit_per_user') ? $request->integer('usage_limit_per_user') : null,
            status: $request->enum('status', GlobalStatusEnum::class),
            productIds: array_map('intval', $request->array('product_ids')),
            categoryIds: array_map('intval', $request->array('category_ids')),
        ));

        return redirect()
            ->route('admin.discounts.index')
            ->with('success', 'کد تخفیف «' . $discount->code . '» ایجاد شد.');
    }

    public function edit(string $discountId): View
    {
        return view('admin.discounts.edit', [
            'discount' => $this->discounts->details((int) $discountId),
            'statusOptions' => $this->statusOptions(),
            'typeOptions' => $this->typeOptions(),
        ]);
    }

    public function update(UpdateAdminDiscountRequest $request, string $discountId): RedirectResponse
    {
        $discount = $this->discounts->update((int) $discountId, new DiscountUpdateData(
            name: $request->string('name')->value(),
            code: $request->string('code')->value(),
            userId: $request->filled('user_id') ? $request->integer('user_id') : null,
            userIdProvided: $request->has('user_id'),
            type: $request->enum('type', DiscountTypeEnum::class),
            amount: $request->integer('amount'),
            minOrderAmount: $request->filled('min_order_amount') ? $request->integer('min_order_amount') : null,
            minOrderAmountProvided: $request->has('min_order_amount'),
            maxDiscountAmount: $request->filled('max_discount_amount') ? $request->integer('max_discount_amount') : null,
            maxDiscountAmountProvided: $request->has('max_discount_amount'),
            startsAt: $request->startsAt(),
            startsAtProvided: $request->has('starts_at'),
            endsAt: $request->endsAt(),
            endsAtProvided: $request->has('ends_at'),
            usageLimit: $request->filled('usage_limit') ? $request->integer('usage_limit') : null,
            usageLimitProvided: $request->has('usage_limit'),
            usageLimitPerUser: $request->filled('usage_limit_per_user') ? $request->integer('usage_limit_per_user') : null,
            usageLimitPerUserProvided: $request->has('usage_limit_per_user'),
            status: $request->enum('status', GlobalStatusEnum::class),
            productIds: array_map('intval', $request->array('product_ids')),
            productIdsProvided: $request->boolean('product_ids_present'),
            categoryIds: array_map('intval', $request->array('category_ids')),
            categoryIdsProvided: $request->boolean('category_ids_present'),
        ));

        return redirect()
            ->route('admin.discounts.index')
            ->with('success', 'کد تخفیف «' . $discount->code . '» ویرایش شد.');
    }

    /** @return array<string, string> */
    private function statusOptions(): array
    {
        return collect(GlobalStatusEnum::toKeyValueType())
            ->mapWithKeys(fn ($item) => [$item->value => $item->key])
            ->all();
    }

    /** @return array<string, string> */
    private function typeOptions(): array
    {
        return collect(DiscountTypeEnum::toKeyValueType())
            ->mapWithKeys(fn ($item) => [$item->value => $item->key])
            ->all();
    }
}
