<?php

namespace App\Livewire\Admin\Products;

use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;

use App\DTO\Product\ProductFilterData;
use App\Enums\GlobalStatusEnum;
use App\Enums\ProductStockFilterEnum;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductTable extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    public string $search = '';

    public string $status = '';

    #[Url(as: 'item_id')]
    public string $itemId = '';

    #[Url(as: 'stock')]
    public string $stockFilter = '';

    public string $colorId = '';

    public string $sizeId = '';

    public string $sortBy = 'id';

    public string $sortDir = 'desc';

    public int $perPage = 15;

    protected ProductServiceInterface $productService;

    public function boot(ProductServiceInterface $productService): void
    {
        $this->productService = $productService;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedItemId(): void
    {
        $this->resetPage();
    }

    public function updatedStockFilter(): void
    {
        $this->resetPage();
    }

    public function updatedColorId(): void
    {
        $this->resetPage();
    }

    public function updatedSizeId(): void
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
        if (! in_array($column, ['id', 'item_id', 'price', 'stock', 'shipping_price', 'status', 'created_at'], true)) {
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
        $this->reset('search', 'status', 'itemId', 'stockFilter', 'colorId', 'sizeId');
        $this->resetPage();
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        return (int) ($this->search !== '')
            + (int) ($this->status !== '')
            + (int) ($this->itemId !== '')
            + (int) ($this->stockFilter !== '')
            + (int) ($this->colorId !== '')
            + (int) ($this->sizeId !== '');
    }

    public function render(): View
    {
        return view('livewire.admin.products.product-table', [
            'products' => $this->productService->filter(new ProductFilterData(
                search: $this->search,
                status: GlobalStatusEnum::tryFrom($this->status),
                itemId: $this->itemId !== '' ? (int) $this->itemId : null,
                stockFilter: ProductStockFilterEnum::tryFrom($this->stockFilter),
                colorId: $this->colorId !== '' ? (int) $this->colorId : null,
                sizeId: $this->sizeId !== '' ? (int) $this->sizeId : null,
                lowStockThreshold: ProductStockFilterEnum::DEFAULT_LOW_THRESHOLD,
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: [$this->sortBy => $this->sortDir],
            )),
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
            'stockFilterOptions' => collect(ProductStockFilterEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
            'itemOptions' => $this->productService->itemOptions(
                ctype_digit($this->itemId) && (int) $this->itemId > 0 ? (int) $this->itemId : null,
            ),
            'colorOptions' => $this->productService->colorFilterOptions(),
            'sizeOptions' => $this->productService->sizeFilterOptions(),
            'lowStockThreshold' => ProductStockFilterEnum::DEFAULT_LOW_THRESHOLD,
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }
}
