<?php

namespace App\Models\Concerns;

use App\DTO\FooterEntryFilterData;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

trait FiltersFooterEntries
{
    public static function filter(FooterEntryFilterData $filters): Paginator
    {
        $column = in_array((new self())->getTable(), ['footer_features', 'footer_link_groups'], true) ? 'title' : 'label';
        $sorts = array_intersect_key($filters->sorts, array_flip(['id', $column, 'sort_order', 'status', 'created_at']));
        $query = self::query()
            ->when($filters->search !== null && $filters->search !== '', fn (Builder $query) => $query->where($column, 'like', '%'.$filters->search.'%'))
            ->when($filters->status !== null, fn (Builder $query) => $query->where('status', $filters->status->value));

        foreach ($sorts ?: ['sort_order' => 'asc'] as $sortColumn => $direction) {
            $query->orderBy($sortColumn, $direction);
        }

        return $query->orderBy('id')->simplePaginate(perPage: $filters->perPage, page: $filters->page)->withQueryString();
    }
}
