<?php

namespace App\Http\Controllers;

use App\DTO\ProductReview\ProductReviewCreateData;
use App\Http\Requests\StoreProductReviewRequest;
use App\Models\User;
use App\Services\Contracts\ProductReviewServiceInterface;
use Illuminate\Http\RedirectResponse;

class ProductReviewController extends Controller
{
    public function __construct(private readonly ProductReviewServiceInterface $reviews) {}

    public function store(StoreProductReviewRequest $request, int $itemId): RedirectResponse
    {
        $user = $request->user('web');

        $this->reviews->submit(
            $itemId,
            $user instanceof User ? $user : null,
            new ProductReviewCreateData(
                rating: $request->integer('rating'),
                title: $request->reviewTitle(),
                body: $request->string('body')->value(),
                guestName: $user === null ? $request->guestName() : null,
                guestEmail: $user === null ? $request->guestEmail() : null,
                positivePoints: $request->positivePoints(),
                negativePoints: $request->negativePoints(),
            ),
        );

        return redirect()->back()->with('review_submitted', 'نظر شما ثبت شد و پس از بررسی نمایش داده می‌شود.');
    }
}
