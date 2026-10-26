<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Order\OrderStatusUpdateData;
use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminOrderStatusRequest;
use App\Services\Contracts\OrderServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    public function __construct(private readonly OrderServiceInterface $orders)
    {
    }

    public function index(): View
    {
        return view('admin.orders.index');
    }

    public function show(int $orderId): View
    {
        return view('admin.orders.show', [
            'order' => $this->orders->detailsForAdmin($orderId),
            'statusOptions' => collect(OrderStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
        ]);
    }

    public function updateStatus(UpdateAdminOrderStatusRequest $request, int $orderId): RedirectResponse
    {
        $this->orders->updateStatus($orderId, new OrderStatusUpdateData(
            status: $request->enum('status', OrderStatusEnum::class),
        ));

        return redirect()
            ->route('admin.orders.show', $orderId)
            ->with('success', 'وضعیت سفارش با موفقیت به‌روزرسانی شد.');
    }
}
