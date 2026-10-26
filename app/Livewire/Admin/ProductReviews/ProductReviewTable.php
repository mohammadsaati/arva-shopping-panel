<?php

namespace App\Livewire\Admin\ProductReviews;

use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;

use App\DTO\ProductReview\ProductReviewFilterData;
use App\Enums\ProductReviewApprovalStatusEnum;
use App\Services\Contracts\ProductReviewServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProductReviewTable extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    public string $sortBy = 'created_at';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    protected ProductReviewServiceInterface $reviews;

    public function boot(ProductReviewServiceInterface $reviews): void
    {
        $this->reviews = $reviews;
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
        if (! in_array($column, ['id', 'rating', 'approval_status', 'created_at'], true)) {
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
        $this->reset('search', 'status');
        $this->resetPage();
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        return (int) ($this->search !== '') + (int) ($this->status !== '');
    }

    public function render(): View
    {
        return view('livewire.admin.product-reviews.product-review-table', [
            'reviews' => $this->reviews->filterForAdmin(new ProductReviewFilterData(
                search: $this->search,
                status: ProductReviewApprovalStatusEnum::tryFrom($this->status),
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: [$this->sortBy => $this->sortDir],
            )),
            'statusOptions' => collect(ProductReviewApprovalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($option) => [$option->value => $option->key])
                ->all(),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }
}
