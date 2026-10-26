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
    GlobalStatusEnum::ACTIVE->value => 'Active',
    GlobalStatusEnum::INACTIVE->value => 'Inactive',
    'blog_post_status' => [
        BlogPostStatusEnum::DRAFT->value => 'Draft',
        BlogPostStatusEnum::PUBLISHED->value => 'Published',
        BlogPostStatusEnum::ARCHIVED->value => 'Archived',
    ],
    'product_review_approval_status' => [
        ProductReviewApprovalStatusEnum::PENDING->value => 'Pending review',
        ProductReviewApprovalStatusEnum::APPROVED->value => 'Approved',
        ProductReviewApprovalStatusEnum::REJECTED->value => 'Rejected',
    ],
    'marketplace_approval_status' => [
        MarketplaceApprovalStatusEnum::PENDING->value => 'Pending review',
        MarketplaceApprovalStatusEnum::APPROVED->value => 'Approved',
        MarketplaceApprovalStatusEnum::REJECTED->value => 'Rejected',
    ],
    'payment_gateway_driver' => [
        PaymentGatewayDriverEnum::ZARINPAL->value => 'Zarinpal',
    ],
    'payment_status' => [
        PaymentStatusEnum::PENDING->value => 'Awaiting payment',
        PaymentStatusEnum::PAID->value => 'Paid',
        PaymentStatusEnum::FAILED->value => 'Failed',
    ],
    'payment_currency' => [
        PaymentCurrencyEnum::IRT->value => 'Toman',
        PaymentCurrencyEnum::IRR->value => 'Rial',
    ],
    'transaction_reason_type' => [
        TransactionReasonTypeEnum::ORDER->value => 'Order payment',
        TransactionReasonTypeEnum::WALLET->value => 'Wallet',
    ],
    'vendor_settlement_status' => [
        VendorSettlementStatusEnum::PENDING->value => 'Pending',
        VendorSettlementStatusEnum::PAID->value => 'Paid',
        VendorSettlementStatusEnum::FAILED->value => 'Failed',
        VendorSettlementStatusEnum::CANCELLED->value => 'Cancelled',
    ],
    'product_review_point_sentiment' => [
        ProductReviewPointSentimentEnum::POSITIVE->value => 'Positive',
        ProductReviewPointSentimentEnum::NEGATIVE->value => 'Negative',
    ],
    BannerTypeEnum::PRODUCT->value => 'Product',
    BannerTypeEnum::CATEGORY->value => 'Category',
    BannerTypeEnum::LINK->value => 'Link',
    SliderTypeEnum::PRODUCT->value => 'Product',
    SliderTypeEnum::CATEGORY->value => 'Category',
    SliderTypeEnum::LINK->value => 'Link',
    SliderTypeEnum::SESSION->value => 'Section',
    UserTypeEnum::NORMAL->value => 'Normal',
    UserTypeEnum::VENDOR->value => 'Vendor',
    'order_status' => [
        OrderStatusEnum::PENDING->value => 'Pending review',
        OrderStatusEnum::PROCESSING->value => 'Processing',
        OrderStatusEnum::ON_HOLD->value => 'On hold',
        OrderStatusEnum::SHIPPED->value => 'Shipped',
        OrderStatusEnum::DELIVERED->value => 'Delivered',
        OrderStatusEnum::CANCELLED->value => 'Cancelled',
    ],
    'discount_type' => [
        DiscountTypeEnum::FIXED->value => 'Fixed amount',
        DiscountTypeEnum::PERCENTAGE->value => 'Percentage',
    ],
    'product_stock_filter' => [
        ProductStockFilterEnum::IN_STOCK->value => 'In stock',
        ProductStockFilterEnum::LOW_STOCK->value => 'Low stock',
        ProductStockFilterEnum::OUT_OF_STOCK->value => 'Out of stock',
    ],
    'specification_type' => [
        SpecificationTypeEnum::TEXT->value => 'Text',
        SpecificationTypeEnum::NUMBER->value => 'Number',
        SpecificationTypeEnum::BOOLEAN->value => 'Yes / No',
        SpecificationTypeEnum::SELECT->value => 'Single choice',
        SpecificationTypeEnum::MULTI_SELECT->value => 'Multiple choices',
    ],
];
