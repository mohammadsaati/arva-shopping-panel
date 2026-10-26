<?php

namespace App\DTO\Province;

use App\DTO\BaseFilterData;
use App\Enums\GlobalStatusEnum;

final readonly class ProvinceFilterData extends BaseFilterData
{
    /**
     * @param array<string, 'asc'|'desc'> $sorts
     */
    public function __construct(
        public ?string $search = null,
        public ?GlobalStatusEnum $status = null,
        int $perPage = 15,
        int $page = 1,
        array $sorts = ['id' => 'desc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
