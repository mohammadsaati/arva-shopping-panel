<?php

namespace App\Services\Contracts;

use App\Models\Order;
use App\Models\User;

interface PaymentServiceInterface
{
    public function start(Order $order): string;

    public function startForUser(User $user, int $orderId): string;

    public function handleZarinpalCallback(string $authority, string $status): Order;
}
