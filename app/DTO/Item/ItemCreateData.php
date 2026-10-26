<?php

namespace App\DTO\Item;

use App\Enums\GlobalStatusEnum;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;

final readonly class ItemCreateData
{
    public function __construct(
        public string $name,
        public string $slug,
        public int $code,
        public int $categoryId,
        public ?int $brandId,
        public ?UploadedFile $image,
        public ?string $description,
        public GlobalStatusEnum $status,
        public bool $isAmazing = false,
        public ?Carbon $amazingEndTime = null,
        public ?ItemProductCreateData $product = null,
        /** @var array<int, \App\DTO\Specification\SpecificationAnswerData> */
        public array $specifications = [],
    ) {
    }
}
