<?php

namespace App\DTO;

use App\Enums\GlobalStatusEnum;

final readonly class NewsletterSubscriberStatusData
{
    public function __construct(public GlobalStatusEnum $status)
    {
    }
}
