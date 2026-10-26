<?php

namespace App\DTO\Banner;

use App\DTO\BaseFilterData;
use App\Enums\BannerTypeEnum;
use App\Enums\GlobalStatusEnum;

final readonly class BannerFilterData extends BaseFilterData
{
    /** @param array<string, 'asc'|'desc'> $sorts */
    public function __construct(
        public ?string $search = null,
        public ?BannerTypeEnum $type = null,
        public ?GlobalStatusEnum $status = null,
        int $perPage = 15,
        int $page = 1,
        array $sorts = ['id' => 'desc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
