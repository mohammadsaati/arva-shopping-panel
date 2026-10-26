<?php

namespace App\Support;

final class ZarinpalUserMessage
{
    public static function forRequest(?int $code, string $fallback): string
    {
        if ($message = self::messageForCode($code, context: 'request')) {
            return $message;
        }

        return self::sanitize($fallback, 'درخواست پرداخت به زرین‌پال انجام نشد. لطفاً چند دقیقه بعد دوباره تلاش کنید.');
    }

    public static function forVerify(?int $code, string $fallback): string
    {
        if ($message = self::messageForCode($code, context: 'verify')) {
            return $message;
        }

        return self::sanitize($fallback, 'تأیید پرداخت انجام نشد. اگر مبلغ از حساب شما کسر شده، چند دقیقه صبر کنید یا با پشتیبانی تماس بگیرید.');
    }

    private static function messageForCode(?int $code, string $context): ?string
    {
        if ($code === null) {
            return null;
        }

        return match ($code) {
            -9 => 'اطلاعات ارسالی به درگاه ناقص یا نامعتبر است.',
            -10, -33 => 'پذیرنده یا درگاه پرداخت فعال نیست. با پشتیبانی فروشگاه تماس بگیرید.',
            -11 => 'درگاه پرداخت غیرفعال است.',
            -12 => 'تعداد تلاش بیش از حد مجاز بود. کمی بعد دوباره امتحان کنید.',
            -15 => 'درگاه پرداخت مسدود شده است.',
            -16 => 'سطح پذیرنده برای این تراکنش کافی نیست.',
            -30 => 'ارز تراکنش برای این درگاه مجاز نیست.',
            -31 => 'اطلاعات پذیرنده ناقص است.',
            -32 => 'مبلغ پرداخت نامعتبر است.',
            -34 => 'شناسه پرداخت (Authority) نامعتبر است.',
            -35 => 'تاریخ یا زمان تراکنش نامعتبر است.',
            -40 => 'اجازه انجام این عملیات وجود ندارد.',
            -50 => 'مبلغ پرداخت با مبلغ سفارش یکسان نیست.',
            -51 => 'پرداخت انجام نشده است.',
            -52 => 'خطا در انجام تراکنش.',
            -53 => 'پرداخت توسط شما لغو شد.',
            -54 => 'شناسه مرجع تراکنش نامعتبر است.',
            101 => $context === 'verify' ? 'این پرداخت قبلاً تأیید شده است.' : null,
            default => null,
        };
    }

    private static function sanitize(string $message, string $genericPersian): string
    {
        $message = trim($message);

        if ($message === '') {
            return $genericPersian;
        }

        if (preg_match('/[\x{0600}-\x{06FF}]/u', $message) === 1) {
            return $message;
        }

        return $genericPersian;
    }
}
