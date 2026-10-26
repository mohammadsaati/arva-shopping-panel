<?php

namespace App\DTO\Blog;

use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class BlogBannerUpdateData
{
    public function __construct(
        public ?string $eyebrow = null,
        public bool $eyebrowProvided = false,
        public ?string $title = null,
        public ?string $description = null,
        public bool $descriptionProvided = false,
        public ?UploadedFile $image = null,
        public ?UploadedFile $mobileImage = null,
        public bool $removeMobileImage = false,
        public ?string $targetUrl = null,
        public bool $targetUrlProvided = false,
        public ?string $targetLabel = null,
        public bool $targetLabelProvided = false,
        public ?int $sortOrder = null,
        public ?string $startsAt = null,
        public bool $startsAtProvided = false,
        public ?string $endsAt = null,
        public bool $endsAtProvided = false,
        public ?GlobalStatusEnum $status = null,
    ) {
    }

    /** @return array<string, string|int|GlobalStatusEnum|null> */
    public function toModelAttributes(): array
    {
        $attributes = array_filter([
            'title' => $this->title,
            'sort_order' => $this->sortOrder,
            'status' => $this->status,
        ], static fn (string|int|GlobalStatusEnum|null $value): bool => $value !== null);

        foreach ([
            'eyebrow' => [$this->eyebrowProvided, $this->eyebrow],
            'description' => [$this->descriptionProvided, $this->description],
            'target_url' => [$this->targetUrlProvided, $this->targetUrl],
            'target_label' => [$this->targetLabelProvided, $this->targetLabel],
            'starts_at' => [$this->startsAtProvided, $this->startsAt],
            'ends_at' => [$this->endsAtProvided, $this->endsAt],
        ] as $attribute => [$provided, $value]) {
            if ($provided) {
                $attributes[$attribute] = $value;
            }
        }

        return $attributes;
    }
}
