<?php

namespace App\DTO;

final readonly class NewsletterSubscriptionData
{
    public function __construct(public string $email)
    {
    }
}
