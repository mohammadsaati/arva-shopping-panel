<?php

namespace App\DTO\Brand;

use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class BrandCreateData
{
    public function __construct(
        public string $name,
        public string $slug,
        public ?UploadedFile $image,
        public GlobalStatusEnum $status,
    ) {
    }
}
