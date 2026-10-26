<?php

namespace App\Livewire\Admin\Specifications;

use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;

use App\DTO\Specification\SpecificationCategoryFilterData;
use App\Services\Contracts\SpecificationServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryTable extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    public string $search = '';

    public string $sortBy = 'name';

    public string $sortDir = 'asc';

    public int $perPage = 15;

    protected SpecificationServiceInterface $specifications;

    public function boot(SpecificationServiceInterface $specifications): void
    {
        $this->specifications = $specifications;
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
        if (! in_array($column, ['id', 'name', 'created_at'], true)) {
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

    public function render(): View
    {
        return view('livewire.admin.specifications.category-table', [
            'categories' => $this->specifications->categories(new SpecificationCategoryFilterData(
                search: trim($this->search) !== '' ? trim($this->search) : null,
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: [$this->sortBy => $this->sortDir],
            )),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }
}
