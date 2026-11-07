<?php

namespace App\Services;

use App\DTO\Commission\CommissionCreateData;
use App\DTO\Commission\CommissionFilterData;
use App\DTO\Commission\CommissionUpdateData;
use App\Models\Commission;
use App\Services\Contracts\CommissionServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Validation\ValidationException;

class CommissionService implements CommissionServiceInterface
{
    public function create(CommissionCreateData $data): Commission
    {
        return Commission::createDefinition($data->title, $this->basisPoints($data->percentage));
    }

    public function update(int $commissionId, CommissionUpdateData $data): Commission
    {
        return Commission::details($commissionId)->updateDefinition(
            $data->title,
            $data->percentage !== null ? $this->basisPoints($data->percentage) : null,
        );
    }

    public function details(int $commissionId): Commission
    {
        return Commission::details($commissionId);
    }

    public function filter(CommissionFilterData $filters): Paginator
    {
        return Commission::filter($filters);
    }

    public function selectionOptions(): array
    {
        return Commission::selectionOptions();
    }

    private function basisPoints(string $percentage): int
    {
        if (preg_match('/^(?:100(?:\.0{1,2})?|(?:0|[1-9][0-9]?)(?:\.[0-9]{1,2})?)$/', $percentage) !== 1) {
            throw ValidationException::withMessages(['percentage' => 'درصد کمیسیون باید بین ۰ تا ۱۰۰ و حداکثر با دو رقم اعشار باشد.']);
        }

        [$whole, $fraction] = array_pad(explode('.', $percentage, 2), 2, '');

        return ((int) $whole * 100) + (int) str_pad($fraction, 2, '0');
    }
}
