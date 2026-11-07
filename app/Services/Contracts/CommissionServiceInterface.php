<?php

namespace App\Services\Contracts;

use App\DTO\Commission\CommissionCreateData;
use App\DTO\Commission\CommissionFilterData;
use App\DTO\Commission\CommissionUpdateData;
use App\Models\Commission;
use Illuminate\Contracts\Pagination\Paginator;

interface CommissionServiceInterface
{
    public function create(CommissionCreateData $data): Commission;

    public function update(int $commissionId, CommissionUpdateData $data): Commission;

    public function details(int $commissionId): Commission;

    public function filter(CommissionFilterData $filters): Paginator;

    /** @return array<int, string> */
    public function selectionOptions(): array;
}
