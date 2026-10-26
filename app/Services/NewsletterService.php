<?php

namespace App\Services;

use App\DTO\NewsletterSubscriptionData;
use App\DTO\NewsletterSubscriberFilterData;
use App\DTO\NewsletterSubscriberStatusData;
use Illuminate\Contracts\Pagination\Paginator;
use App\Models\NewsletterSubscriber;
use App\Services\Contracts\NewsletterServiceInterface;

class NewsletterService implements NewsletterServiceInterface
{
    public function subscribe(NewsletterSubscriptionData $data): void
    {
        NewsletterSubscriber::subscribe($data->email);
    }

    public function subscribers(NewsletterSubscriberFilterData $filters): Paginator
    {
        return NewsletterSubscriber::filter($filters);
    }

    public function updateStatus(int $id, NewsletterSubscriberStatusData $data): void
    {
        NewsletterSubscriber::updateStatus($id, $data);
    }
}
