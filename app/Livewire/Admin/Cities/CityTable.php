<?php

namespace App\Livewire\Admin\Cities;

use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;

use App\DTO\City\CityFilterData;
use App\Enums\GlobalStatusEnum;
use App\Services\Contracts\CityServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class CityTable extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    public string $search = '';

    public string $provinceId = '';

    public string $status = '';

    public string $sortBy = 'id';

    public string $sortDir = 'desc';

    public int $perPage = 15;

    protected CityServiceInterface $cityService;

    public function boot(CityServiceInterface $cityService): void
    {
        $this->cityService = $cityService;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedProvinceId(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, [10, 15, 25, 50], true)) {
            $this->perPage = 15;
        }

        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if (! in_array($column, ['id', 'name', 'slug', 'province_id', 'status', 'created_at'], true)) {
            return;
        }

        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDir = 'asc';
        }

        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset('search', 'provinceId', 'status');
        $this->resetPage();
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        return (int) ($this->search !== '')
            + (int) ($this->provinceId !== '')
            + (int) ($this->status !== '');
    }

    public function render(): View
    {
        return view('livewire.admin.cities.city-table', [
            'cities' => $this->cityService->filter(new CityFilterData(
                search: $this->search,
                provinceId: $this->provinceId !== '' ? (int) $this->provinceId : null,
                status: GlobalStatusEnum::tryFrom($this->status),
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: [$this->sortBy => $this->sortDir],
            )),
            'provinceOptions' => $this->cityService->formOptions()['provinces'],
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }
}
