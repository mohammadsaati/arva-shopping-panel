<?php

namespace App\DTO\Blog;

use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class BlogBannerCreateData
{
    public function __construct(
        public ?string $eyebrow,
        public string $title,
        public ?string $description,
        public UploadedFile $image,
        public ?UploadedFile $mobileImage,
        public ?string $targetUrl,
        public ?string $targetLabel,
        public int $sortOrder,
        public ?string $startsAt,
        public ?string $endsAt,
        public GlobalStatusEnum $status,
    ) {
    }
}
