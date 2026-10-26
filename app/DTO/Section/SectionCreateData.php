<?php

namespace App\DTO\Section;

use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class SectionCreateData
{
    /**
     * @param array<int, UploadedFile> $images
     * @param array<int, int> $itemIds
     */
    public function __construct(
        public string $name,
        public string $slug,
        public ?string $bgColor,
        public ?UploadedFile $bgImage,
        public array $images,
        public GlobalStatusEnum $status,
        public array $itemIds = [],
    ) {
    }
}
