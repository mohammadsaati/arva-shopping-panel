<?php

use App\Enums\GlobalStatusEnum;
use App\Enums\BlogPostStatusEnum;
use App\Enums\ProductReviewApprovalStatusEnum;
use App\Enums\ProductReviewPointSentimentEnum;
use App\Enums\OrderStatusEnum;
use App\Enums\BannerTypeEnum;
use App\Enums\SliderTypeEnum;
use App\Enums\UserTypeEnum;
use App\Enums\DiscountTypeEnum;
use App\Enums\SpecificationTypeEnum;
use App\Enums\MarketplaceApprovalStatusEnum;
use App\Enums\VendorSettlementStatusEnum;
use App\Enums\PaymentGatewayDriverEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PaymentCurrencyEnum;
use App\Enums\ProductStockFilterEnum;
use App\Enums\TransactionReasonTypeEnum;

return [
    GlobalStatusEnum::ACTIVE->value => 'فعال',
    GlobalStatusEnum::INACTIVE->value => 'غیرفعال',
    'blog_post_status' => [
        BlogPostStatusEnum::DRAFT->value => 'پیش‌نویس',
        BlogPostStatusEnum::PUBLISHED->value => 'منتشرشده',
        BlogPostStatusEnum::ARCHIVED->value => 'بایگانی‌شده',
    ],
    'product_review_approval_status' => [
        ProductReviewApprovalStatusEnum::PENDING->value => 'در انتظار تأیید',
        ProductReviewApprovalStatusEnum::APPROVED->value => 'تأییدشده',
        ProductReviewApprovalStatusEnum::REJECTED->value => 'ردشده',
    ],
    'marketplace_approval_status' => [
        MarketplaceApprovalStatusEnum::PENDING->value => 'در انتظار تأیید',
        MarketplaceApprovalStatusEnum::APPROVED->value => 'تأییدشده',
        MarketplaceApprovalStatusEnum::REJECTED->value => 'ردشده',
    ],
    'payment_gateway_driver' => [
        PaymentGatewayDriverEnum::ZARINPAL->value => 'زرین‌پال',
    ],
    'payment_status' => [
        PaymentStatusEnum::PENDING->value => 'در انتظار پرداخت',
        PaymentStatusEnum::PAID->value => 'پرداخت‌شده',
        PaymentStatusEnum::FAILED->value => 'ناموفق',
    ],
    'payment_currency' => [
        PaymentCurrencyEnum::IRT->value => 'تومان',
        PaymentCurrencyEnum::IRR->value => 'ریال',
    ],
    'transaction_reason_type' => [
        TransactionReasonTypeEnum::ORDER->value => 'پرداخت سفارش',
        TransactionReasonTypeEnum::WALLET->value => 'کیف پول',
    ],
    'vendor_settlement_status' => [
        VendorSettlementStatusEnum::PENDING->value => 'در انتظار تسویه',
        VendorSettlementStatusEnum::PAID->value => 'پرداخت‌شده',
        VendorSettlementStatusEnum::FAILED->value => 'ناموفق',
        VendorSettlementStatusEnum::CANCELLED->value => 'لغوشده',
    ],
    'product_review_point_sentiment' => [
        ProductReviewPointSentimentEnum::POSITIVE->value => 'نکته مثبت',
        ProductReviewPointSentimentEnum::NEGATIVE->value => 'نکته منفی',
    ],
    BannerTypeEnum::PRODUCT->value => 'محصول',
    BannerTypeEnum::CATEGORY->value => 'دسته‌بندی',
    BannerTypeEnum::LINK->value => 'پیوند',
    SliderTypeEnum::PRODUCT->value => 'محصول',
    SliderTypeEnum::CATEGORY->value => 'دسته‌بندی',
    SliderTypeEnum::LINK->value => 'پیوند',
    SliderTypeEnum::SESSION->value => 'بخش فروشگاه',
    UserTypeEnum::NORMAL->value => 'عادی',
    UserTypeEnum::VENDOR->value => 'فروشنده',
    'order_status' => [
        OrderStatusEnum::PENDING->value => 'در انتظار بررسی',
        OrderStatusEnum::PROCESSING->value => 'در حال آماده‌سازی',
        OrderStatusEnum::ON_HOLD->value => 'متوقف‌شده',
        OrderStatusEnum::SHIPPED->value => 'ارسال‌شده',
        OrderStatusEnum::DELIVERED->value => 'تحویل‌شده',
        OrderStatusEnum::CANCELLED->value => 'لغوشده',
    ],
    'discount_type' => [
        DiscountTypeEnum::FIXED->value => 'مبلغ ثابت',
        DiscountTypeEnum::PERCENTAGE->value => 'درصدی',
    ],
    'product_stock_filter' => [
        ProductStockFilterEnum::IN_STOCK->value => 'موجود',
        ProductStockFilterEnum::LOW_STOCK->value => 'موجودی کم',
        ProductStockFilterEnum::OUT_OF_STOCK->value => 'ناموجود',
    ],
    'specification_type' => [
        SpecificationTypeEnum::TEXT->value => 'متن',
        SpecificationTypeEnum::NUMBER->value => 'عدد',
        SpecificationTypeEnum::BOOLEAN->value => 'بله / خیر',
        SpecificationTypeEnum::SELECT->value => 'انتخاب تکی',
        SpecificationTypeEnum::MULTI_SELECT->value => 'انتخاب چندگانه',
    ],
];
