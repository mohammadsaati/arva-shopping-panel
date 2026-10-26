<?php

namespace App\DTO;

use App\Enums\GlobalStatusEnum;
use Illuminate\Http\UploadedFile;

final readonly class FooterEntryData
{
    public function __construct(
        public string $kind,
        public ?string $title,
        public ?string $label,
        public ?string $url,
        public ?string $icon,
        public ?int $groupId,
        public bool $opensInNewTab,
        public int $sortOrder,
        public GlobalStatusEnum $status,
        public ?UploadedFile $image,
        public bool $removeImage,
        public array $providedFields,
    ) {
    }

    /** @return array<string, mixed> */
    public function attributes(): array
    {
        $attributes = ['sort_order' => $this->sortOrder, 'status' => $this->status];

        $attributes += match ($this->kind) {
            'features' => ['title' => $this->title, 'icon' => $this->icon, 'url' => $this->url],
            'groups' => ['title' => $this->title],
            'links' => ['label' => $this->label, 'url' => $this->url, 'footer_link_group_id' => $this->groupId, 'opens_in_new_tab' => $this->opensInNewTab],
            'social' => ['label' => $this->label, 'url' => $this->url, 'icon' => $this->icon],
            'apps' => ['label' => $this->label, 'url' => $this->url],
        };

        return array_intersect_key($attributes, array_flip([...$this->providedFields, 'sort_order', 'status']));
    }
}
