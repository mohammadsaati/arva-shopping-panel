<?php

namespace App\Jobs;

use App\Services\Contracts\OrderServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExpireUnpaidOrdersJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public int $timeout = 120;

    public function __construct(
        public ?int $olderThanHours = null,
    ) {
        $this->onQueue((string) env('HORIZON_QUEUE', 'default'));
    }

    public function handle(OrderServiceInterface $orders): void
    {
        $hours = $this->olderThanHours ?? (int) config('shop.unpaid_order_expire_hours', 48);

        $orders->expireStaleUnpaid(max(1, $hours));
    }
}
