<?php

namespace App\Livewire\Admin\Commissions;

use App\DTO\Commission\CommissionFilterData;
use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;
use App\Services\Contracts\CommissionServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class CommissionTable extends Component
{
    use EnsuresAdminIsAuthenticated;
    use WithPagination;

    public string $search = '';

    public string $sortBy = 'id';

    public string $sortDir = 'desc';

    public int $perPage = 15;

    protected CommissionServiceInterface $commissions;

    public function boot(CommissionServiceInterface $commissions): void
    {
        $this->commissions = $commissions;
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
        if (! in_array($column, ['id', 'title', 'rate_bps', 'created_at'], true)) {
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
        return view('livewire.admin.commissions.commission-table', [
            'commissions' => $this->commissions->filter(new CommissionFilterData(
                search: $this->search,
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: [$this->sortBy => $this->sortDir],
            )),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }
}
