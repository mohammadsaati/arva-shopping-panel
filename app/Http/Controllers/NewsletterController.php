<?php

namespace App\Http\Controllers;

use App\DTO\NewsletterSubscriptionData;
use App\Http\Requests\SubscribeNewsletterRequest;
use App\Services\Contracts\NewsletterServiceInterface;
use Illuminate\Http\RedirectResponse;

class NewsletterController extends Controller
{
    public function __construct(private readonly NewsletterServiceInterface $newsletter)
    {
    }

    public function store(SubscribeNewsletterRequest $request): RedirectResponse
    {
        $this->newsletter->subscribe(new NewsletterSubscriptionData(
            email: $request->string('email')->value(),
        ));

        return back()->with('newsletter_success', 'عضویت شما در خبرنامه ثبت شد.');
    }
}
