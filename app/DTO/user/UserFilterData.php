<?php

namespace App\DTO\user;

use App\DTO\BaseFilterData;
use App\Enums\GlobalStatusEnum;
use App\Enums\UserTypeEnum;

final readonly class UserFilterData extends BaseFilterData
{
    /**
     * @param array<string, 'asc'|'desc'> $sorts
     */
    public function __construct(
        public string|null $search = null,
        public GlobalStatusEnum|null $status = null,
        public UserTypeEnum|null $type = null,
        int $perPage = 15,
        int $page = 1,
        array $sorts = ['id' => 'desc'],
    ) {

        parent::__construct(
            perPage: $perPage,
            page: $page,
            sorts: $sorts,
        );
    }
}
