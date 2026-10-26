<?php

namespace App\Services\Contracts;

use App\DTO\NewsletterSubscriptionData;
use App\DTO\NewsletterSubscriberFilterData;
use App\DTO\NewsletterSubscriberStatusData;
use Illuminate\Contracts\Pagination\Paginator;

interface NewsletterServiceInterface
{
    public function subscribe(NewsletterSubscriptionData $data): void;

    public function subscribers(NewsletterSubscriberFilterData $filters): Paginator;

    public function updateStatus(int $id, NewsletterSubscriberStatusData $data): void;
}
