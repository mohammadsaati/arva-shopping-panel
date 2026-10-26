<?php

namespace App\DTO\Slider;

use App\Enums\GlobalStatusEnum;
use App\Enums\SliderTypeEnum;
use Illuminate\Http\UploadedFile;

final readonly class SliderCreateData
{
    public function __construct(
        public string $title,
        public int $priority,
        public UploadedFile $image,
        public SliderTypeEnum $type,
        public ?int $productId,
        public ?int $categoryId,
        public ?int $sectionId,
        public ?string $link,
        public GlobalStatusEnum $status,
    ) {
    }
}
