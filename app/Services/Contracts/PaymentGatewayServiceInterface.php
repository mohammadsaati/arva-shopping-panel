<?php

namespace App\Services\Contracts;

use App\DTO\Payment\PaymentGatewayFilterData;
use App\DTO\Payment\PaymentGatewayUpdateData;
use App\Models\PaymentGateway;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;

interface PaymentGatewayServiceInterface
{
    public function filter(PaymentGatewayFilterData $filters): Paginator;

    public function details(int $gatewayId): PaymentGateway;

    public function update(int $gatewayId, PaymentGatewayUpdateData $data): PaymentGateway;

    public function toggleStatus(int $gatewayId): PaymentGateway;

    /** @return Collection<int, PaymentGateway> */
    public function activeForCheckout(): Collection;

    public function findActive(int $gatewayId): ?PaymentGateway;
}
