<?php

namespace App\DTO\Color;

use App\DTO\BaseFilterData;

final readonly class ColorFilterData extends BaseFilterData
{
    /**
     * @param array<string, 'asc'|'desc'> $sorts
     */
    public function __construct(
        public ?string $search = null,
        int $perPage = 15,
        int $page = 1,
        array $sorts = ['id' => 'desc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
