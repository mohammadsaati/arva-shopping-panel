<?php

namespace App\Services\Contracts;

use App\DTO\Order\PlaceOrderData;
use App\DTO\Order\OrderFilterData;
use App\DTO\Order\OrderHistoryFilterData;
use App\DTO\Order\OrderStatusUpdateData;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

interface OrderServiceInterface
{
    public function place(User $user, string $shoppingKey, PlaceOrderData $data): Order;

    public function details(User $user, int $orderId): Order;

    public function filterForAdmin(OrderFilterData $filters): Paginator;

    public function history(User $user, OrderHistoryFilterData $filters): Paginator;

    public function detailsForAdmin(int $orderId): Order;

    public function updateStatus(int $orderId, OrderStatusUpdateData $data): Order;

    public function pendingCount(): int;

    /**
     * @return list<int> Cancelled order IDs
     */
    public function expireStaleUnpaid(int $olderThanHours): array;
}
