<?php

namespace App\Livewire\Admin\VendorProducts;

use App\DTO\VendorProduct\VendorProductAdminFilterData;
use App\Enums\MarketplaceApprovalStatusEnum;
use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;
use App\Services\Contracts\VendorProductApprovalServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class VendorProductTable extends Component
{
    use EnsuresAdminIsAuthenticated;
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = 'pending';

    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    protected VendorProductApprovalServiceInterface $vendorProducts;

    public function boot(VendorProductApprovalServiceInterface $vendorProducts): void
    {
        $this->vendorProducts = $vendorProducts;
    }

    public function updatedSearch(): void
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
        if (! in_array($column, ['id', 'price', 'stock', 'approval_status', 'created_at'], true)) {
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
        $this->search = '';
        $this->status = '';
        $this->resetPage();
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        return (int) ($this->search !== '') + (int) ($this->status !== '');
    }

    public function render(): View
    {
        return view('livewire.admin.vendor-products.vendor-product-table', [
            'vendorProducts' => $this->vendorProducts->filterForAdmin(new VendorProductAdminFilterData(
                search: $this->search,
                status: MarketplaceApprovalStatusEnum::tryFrom($this->status),
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: [$this->sortBy => $this->sortDir],
            )),
            'statusOptions' => collect(MarketplaceApprovalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($option) => [$option->value => $option->key])
                ->all(),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }
}
