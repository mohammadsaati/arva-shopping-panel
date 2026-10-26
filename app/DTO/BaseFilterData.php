<?php

namespace App\DTO;

abstract readonly class BaseFilterData
{
    public int $perPage;

    public int $page;

    /** @var array<string, 'asc'|'desc'> */
    public array $sorts;

    /**
     * @param array<string, 'asc'|'desc'> $sorts
     */
    public function __construct(
        int $perPage = 15,
        int $page = 1,
        array $sorts = ['id' => 'desc'],
    ) {
        $this->perPage = min(100, max(1, $perPage));
        $this->page = max(1, $page);
        $this->sorts = $this->normalizeSorts($sorts);
    }

    /**
     * @param array<string, mixed> $sorts
     * @return array<string, 'asc'|'desc'>
     */
    private function normalizeSorts(array $sorts): array
    {
        $normalized = [];

        foreach ($sorts as $column => $direction) {
            if (! is_string($column) || ! is_string($direction)) {
                continue;
            }

            $direction = strtolower($direction);
            if (in_array($direction, ['asc', 'desc'], true)) {
                $normalized[$column] = $direction;
            }
        }

        return $normalized;
    }
}
