<?php

namespace App\Services;

use App\DTO\Payment\ZarinpalPaymentRequestResult;
use App\DTO\Payment\ZarinpalPaymentVerifyResult;
use App\Services\Contracts\ZarinpalClientInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class ZarinpalClient implements ZarinpalClientInterface
{
    public static function requestUrl(bool $sandbox): string
    {
        return $sandbox
            ? 'https://sandbox.zarinpal.com/pg/v4/payment/request.json'
            : 'https://payment.zarinpal.com/pg/v4/payment/request.json';
    }

    public static function verifyUrl(bool $sandbox): string
    {
        return $sandbox
            ? 'https://sandbox.zarinpal.com/pg/v4/payment/verify.json'
            : 'https://payment.zarinpal.com/pg/v4/payment/verify.json';
    }

    public static function paymentPageUrl(string $authority, bool $sandbox): string
    {
        $base = $sandbox
            ? 'https://sandbox.zarinpal.com/pg/StartPay/'
            : 'https://payment.zarinpal.com/pg/StartPay/';

        return $base.$authority;
    }

    public function requestPayment(
        string $merchantId,
        int $amount,
        string $callbackUrl,
        string $description,
        string $currency,
        bool $sandbox,
        array $metadata = [],
    ): ZarinpalPaymentRequestResult {
        try {
            $response = Http::asJson()
                ->acceptJson()
                ->connectTimeout(5)
                ->timeout(15)
                ->post(self::requestUrl($sandbox), array_filter([
                    'merchant_id' => $merchantId,
                    'amount' => $amount,
                    'callback_url' => $callbackUrl,
                    'description' => $description,
                    'currency' => $currency,
                    'metadata' => $metadata !== [] ? $metadata : null,
                ], static fn (mixed $value): bool => $value !== null));

            $payload = $response->json();

            return ZarinpalPaymentRequestResult::fromPayload(
                is_array($payload) ? $payload : null,
                $response->status(),
            );
        } catch (Throwable $exception) {
            Log::warning('Zarinpal payment request failed.', ['exception' => $exception::class]);

            return new ZarinpalPaymentRequestResult(
                ok: false,
                authority: null,
                code: null,
                message: 'ارتباط با زرین‌پال برقرار نشد. کمی بعد دوباره تلاش کنید.',
            );
        }
    }

    public function verifyPayment(
        string $merchantId,
        int $amount,
        string $authority,
        bool $sandbox,
    ): ZarinpalPaymentVerifyResult {
        try {
            $response = Http::asJson()
                ->acceptJson()
                ->connectTimeout(5)
                ->timeout(15)
                ->post(self::verifyUrl($sandbox), [
                    'merchant_id' => $merchantId,
                    'amount' => $amount,
                    'authority' => $authority,
                ]);

            $payload = $response->json();

            return ZarinpalPaymentVerifyResult::fromPayload(
                is_array($payload) ? $payload : null,
                $response->status(),
            );
        } catch (Throwable $exception) {
            Log::warning('Zarinpal payment verify failed.', ['exception' => $exception::class]);

            return new ZarinpalPaymentVerifyResult(
                verified: false,
                alreadyVerified: false,
                refId: null,
                cardPan: null,
                code: null,
                message: 'ارتباط با زرین‌پال برای تأیید پرداخت برقرار نشد.',
            );
        }
    }

    public function startPayUrl(string $authority, bool $sandbox): string
    {
        return self::paymentPageUrl($authority, $sandbox);
    }
}
