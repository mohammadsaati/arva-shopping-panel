<?php

namespace App\Livewire\Vendor\Items;

use App\DTO\VendorPanel\VendorItemFilterData;
use App\Livewire\Concerns\EnsuresVendorIsAuthenticated;
use App\Services\Contracts\VendorPanelServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class ItemTable extends Component
{
    use EnsuresVendorIsAuthenticated;
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'id';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    protected VendorPanelServiceInterface $panel;

    public function boot(VendorPanelServiceInterface $panel): void
    {
        $this->panel = $panel;
    }

    public function updatedSearch(): void { $this->resetPage(); }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, [10, 15, 25, 50], true)) $this->perPage = 15;
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if (! in_array($column, ['id', 'name', 'created_at'], true)) return;
        $this->sortDir = $this->sortBy === $column && $this->sortDir === 'asc' ? 'desc' : 'asc';
        $this->sortBy = $column;
        $this->resetPage();
    }

    public function resetFilters(): void { $this->reset('search'); $this->resetPage(); }

    #[Computed]
    public function activeFilterCount(): int { return (int) ($this->search !== ''); }

    public function render(): View
    {
        return view('livewire.vendor.items.item-table', [
            'items' => $this->panel->items(Auth::user(), new VendorItemFilterData(
                search: $this->search, perPage: $this->perPage, page: $this->getPage(),
                sorts: [$this->sortBy => $this->sortDir],
            )),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }
}
