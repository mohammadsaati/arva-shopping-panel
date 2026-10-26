<?php

namespace App\Services;

use App\DTO\Payment\PaymentGatewayFilterData;
use App\DTO\Payment\PaymentGatewayUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Models\PaymentGateway;
use App\Services\Contracts\PaymentGatewayServiceInterface;
use App\Support\PaymentGatewayActivation;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Throwable;

class PaymentGatewayService implements PaymentGatewayServiceInterface
{
    public function filter(PaymentGatewayFilterData $filters): Paginator
    {
        return PaymentGateway::filter($filters);
    }

    public function details(int $gatewayId): PaymentGateway
    {
        return PaymentGateway::details($gatewayId);
    }

    public function update(int $gatewayId, PaymentGatewayUpdateData $data): PaymentGateway
    {
        $gateway = PaymentGateway::details($gatewayId);
        PaymentGatewayActivation::assertReady(
            $gateway->driver,
            $data->status ?? $gateway->status,
            $data->appliedSettings($gateway->settings),
        );

        $oldImagePath = $gateway->image;
        $newImagePath = null;

        if ($data->image !== null) {
            $storedImagePath = $data->image->store('payment-gateways', 'public');

            if (! is_string($storedImagePath)) {
                throw new RuntimeException('The payment gateway image could not be saved.');
            }

            $newImagePath = $storedImagePath;
        }

        try {
            $gateway->updateDetails($data, $newImagePath);
        } catch (Throwable $exception) {
            if ($newImagePath !== null) {
                Storage::disk('public')->delete($newImagePath);
            }

            throw $exception;
        }

        if (($newImagePath !== null || $data->removeImage) && $oldImagePath !== null) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return $gateway;
    }

    public function toggleStatus(int $gatewayId): PaymentGateway
    {
        $gateway = PaymentGateway::details($gatewayId);
        $nextStatus = $gateway->status === GlobalStatusEnum::ACTIVE
            ? GlobalStatusEnum::INACTIVE
            : GlobalStatusEnum::ACTIVE;
        PaymentGatewayActivation::assertReady(
            $gateway->driver,
            $nextStatus,
            $gateway->settings,
        );

        return $gateway->toggleStatus();
    }

    public function activeForCheckout(): Collection
    {
        return PaymentGateway::activeForCheckout();
    }

    public function findActive(int $gatewayId): ?PaymentGateway
    {
        return PaymentGateway::findActive($gatewayId);
    }
}
