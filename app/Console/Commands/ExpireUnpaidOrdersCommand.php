<?php

namespace App\Console\Commands;

use App\Jobs\ExpireUnpaidOrdersJob;
use App\Services\Contracts\OrderServiceInterface;
use Illuminate\Console\Command;

class ExpireUnpaidOrdersCommand extends Command
{
    protected $signature = 'orders:expire-unpaid
                            {--hours= : Override SHOP_UNPAID_ORDER_EXPIRE_HOURS}
                            {--sync : Run immediately in this process instead of the queue}';

    protected $description = 'Queue (or run) cancellation of stale unpaid orders';

    public function __construct(private readonly OrderServiceInterface $orders)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $hours = $this->option('hours') !== null
            ? max(1, (int) $this->option('hours'))
            : null;

        if ($this->option('sync')) {
            $resolvedHours = $hours ?? (int) config('shop.unpaid_order_expire_hours', 48);
            $expiredIds = $this->orders->expireStaleUnpaid($resolvedHours);
            $this->info(sprintf(
                'Cancelled %d unpaid order(s) older than %d hour(s).',
                count($expiredIds),
                $resolvedHours,
            ));

            return self::SUCCESS;
        }

        ExpireUnpaidOrdersJob::dispatch($hours);
        $this->info('Expire-unpaid job dispatched to the queue for Horizon workers.');

        return self::SUCCESS;
    }
}
