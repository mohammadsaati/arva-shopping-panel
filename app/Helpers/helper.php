<?php

use App\DTO\KeyValueType;

if (!function_exists('versioned_assets')) {
    function versioned_assets(string $file): string
    {
        $filePath = public_path($file);
        $version = file_exists($filePath) ? filemtime($filePath) : '';

        return asset($file) . '?v=' . $version;
    }
}

if (!function_exists('key_value_type_object')) {
    function key_value_type_object(string $title, mixed $value): KeyValueType
    {
        return new KeyValueType($title, $value);
    }
}

if (! function_exists('public_image_url')) {
    function public_image_url(?string $path, string $fallback = 'assets/media/images/1.jpg'): string
    {
        $candidate = ($path === null || trim($path) === '') ? $fallback : trim($path);

        if (str_starts_with($candidate, 'http://') || str_starts_with($candidate, 'https://')) {
            return $candidate;
        }

        if (str_starts_with($candidate, '/')) {
            return $candidate;
        }

        if (str_starts_with($candidate, 'assets/')) {
            return '/'.$candidate;
        }

        $normalized = ltrim($candidate, '/');

        if (str_starts_with($normalized, 'storage/')) {
            return '/'.$normalized;
        }

        return '/storage/'.$normalized;
    }
}

if (! function_exists('jalali_date')) {
    function jalali_date(\DateTimeInterface|string|int|null $date, string $format = 'Y/m/d'): ?string
    {
        if ($date === null) {
            return null;
        }

        if (is_int($date)) {
            $date = '@' . $date;
        }

        if ($date instanceof \DateTimeInterface) {
            $date = \DateTime::createFromInterface($date);
        }

        return verta($date)->format($format);
    }
}

if (! function_exists('site_name')) {
    function site_name(): string
    {
        return app(\App\Services\Contracts\SiteSettingServiceInterface::class)->siteName();
    }
}

if (! function_exists('site_settings')) {
    function site_settings(): ?\App\Models\SiteSetting
    {
        return app(\App\Services\Contracts\SiteSettingServiceInterface::class)->current();
    }
}

if (! function_exists('phone_href')) {
    function phone_href(string $phone): string
    {
        $western = strtr($phone, [
            '۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4',
            '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
            '٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4',
            '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
        ]);

        return 'tel:'.preg_replace('/[^0-9+]/', '', $western);
    }
}
