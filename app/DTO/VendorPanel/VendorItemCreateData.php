<?php

namespace App\DTO\VendorPanel;

use Illuminate\Http\UploadedFile;

final readonly class VendorItemCreateData
{
    public function __construct(
        public string $name,
        public string $slug,
        public int $code,
        public int $categoryId,
        public ?int $brandId,
        public ?UploadedFile $image,
        public ?string $description,
    ) {
    }
}
