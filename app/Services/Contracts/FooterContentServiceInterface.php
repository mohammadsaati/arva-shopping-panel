<?php

namespace App\Services\Contracts;

use App\DTO\FooterEntryData;
use App\DTO\FooterEntryFilterData;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;

interface FooterContentServiceInterface
{
    /** @return array<string, string> */
    public function kinds(): array;

    public function entry(string $kind, int $id): Model;

    /** @return array<int, string> */
    public function groupOptions(): array;

    public function filter(string $kind, FooterEntryFilterData $filters): Paginator;

    public function save(FooterEntryData $data, ?int $id = null): void;

    public function delete(string $kind, int $id): void;
}
