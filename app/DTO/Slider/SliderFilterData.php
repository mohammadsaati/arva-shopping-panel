<?php

namespace App\DTO\Slider;

use App\DTO\BaseFilterData;
use App\Enums\GlobalStatusEnum;
use App\Enums\SliderTypeEnum;

final readonly class SliderFilterData extends BaseFilterData
{
    /** @param array<string, 'asc'|'desc'> $sorts */
    public function __construct(
        public ?string $search = null,
        public ?SliderTypeEnum $type = null,
        public ?GlobalStatusEnum $status = null,
        int $perPage = 15,
        int $page = 1,
        array $sorts = ['id' => 'desc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
