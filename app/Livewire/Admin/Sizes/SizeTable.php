<?php

namespace App\Livewire\Admin\Sizes;

use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;

use App\DTO\Size\SizeFilterData;
use App\Services\Contracts\SizeServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class SizeTable extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    public string $search = '';

    public string $sortBy = 'id';

    public string $sortDir = 'desc';

    public int $perPage = 15;

    protected SizeServiceInterface $sizeService;

    public function boot(SizeServiceInterface $sizeService): void
    {
        $this->sizeService = $sizeService;
    }

    public function updatedSearch(): void
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
        if (! in_array($column, ['id', 'value', 'created_at'], true)) {
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
        $this->reset('search');
        $this->resetPage();
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        return (int) ($this->search !== '');
    }

    public function render(): View
    {
        return view('livewire.admin.sizes.size-table', [
            'sizes' => $this->sizeService->filter(new SizeFilterData(
                search: $this->search,
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: [$this->sortBy => $this->sortDir],
            )),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }
}
