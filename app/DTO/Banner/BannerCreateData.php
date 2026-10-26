<?php

namespace App\DTO\Banner;

use App\Enums\BannerTypeEnum;
use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class BannerCreateData
{
    public function __construct(
        public UploadedFile $image,
        public BannerTypeEnum $type,
        public ?int $productId,
        public ?int $categoryId,
        public ?string $link,
        public GlobalStatusEnum $status,
    ) {
    }
}
