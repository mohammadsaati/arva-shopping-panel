<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ExpireUnpaidOrdersJob;
use App\Services\Contracts\OrderServiceInterface;
use Mockery;
use Tests\TestCase;

class ExpireUnpaidOrdersJobTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_handle_calls_order_service_with_configured_hours(): void
    {
        config(['shop.unpaid_order_expire_hours' => 36]);

        $orders = Mockery::mock(OrderServiceInterface::class);
        $orders->shouldReceive('expireStaleUnpaid')->once()->with(36)->andReturn([]);

        (new ExpireUnpaidOrdersJob)->handle($orders);
    }

    public function test_handle_uses_constructor_hours_when_provided(): void
    {
        config(['shop.unpaid_order_expire_hours' => 36]);

        $orders = Mockery::mock(OrderServiceInterface::class);
        $orders->shouldReceive('expireStaleUnpaid')->once()->with(12)->andReturn([1]);

        (new ExpireUnpaidOrdersJob(12))->handle($orders);
    }
}
