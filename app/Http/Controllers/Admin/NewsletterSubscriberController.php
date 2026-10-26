<?php

namespace App\Http\Controllers\Admin;

use App\DTO\NewsletterSubscriberStatusData;
use App\Enums\GlobalStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateNewsletterSubscriberStatusRequest;
use App\Services\Contracts\NewsletterServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class NewsletterSubscriberController extends Controller
{
    public function __construct(private readonly NewsletterServiceInterface $newsletter)
    {
    }

    public function index(): View
    {
        return view('admin.settings.newsletter.index');
    }

    public function update(UpdateNewsletterSubscriberStatusRequest $request, string $subscriberId): RedirectResponse
    {
        $this->newsletter->updateStatus((int) $subscriberId, new NewsletterSubscriberStatusData(
            status: $request->enum('status', GlobalStatusEnum::class),
        ));

        return back()->with('success', 'وضعیت عضو خبرنامه تغییر کرد.');
    }
}
