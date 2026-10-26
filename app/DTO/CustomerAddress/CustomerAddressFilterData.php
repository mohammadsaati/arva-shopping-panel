<?php

namespace App\DTO\CustomerAddress;

use App\DTO\BaseFilterData;

final readonly class CustomerAddressFilterData extends BaseFilterData
{
    /**
     * @param array<string, 'asc'|'desc'> $sorts
     */
    public function __construct(
        int $perPage = 10,
        int $page = 1,
        array $sorts = ['id' => 'desc'],
    ) {
        parent::__construct(perPage: $perPage, page: $page, sorts: $sorts);
    }
}
