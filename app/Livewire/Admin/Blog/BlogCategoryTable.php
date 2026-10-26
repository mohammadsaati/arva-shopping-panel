<?php

namespace App\Livewire\Admin\Blog;

use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;

use App\DTO\Blog\BlogCategoryFilterData;
use App\Enums\GlobalStatusEnum;
use App\Services\Contracts\BlogManagementServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class BlogCategoryTable extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $sortBy = 'id';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    protected BlogManagementServiceInterface $blog;

    public function boot(BlogManagementServiceInterface $blog): void { $this->blog = $blog; }
    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, [10, 15, 25, 50], true)) $this->perPage = 15;
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if (! in_array($column, ['id', 'name', 'slug', 'priority', 'status', 'created_at'], true)) return;
        $this->sortDir = $this->sortBy === $column && $this->sortDir === 'asc' ? 'desc' : 'asc';
        $this->sortBy = $column;
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
        return view('livewire.admin.blog.blog-category-table', [
            'categories' => $this->blog->filterCategories(new BlogCategoryFilterData(
                search: $this->search,
                status: GlobalStatusEnum::tryFrom($this->status),
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: [$this->sortBy => $this->sortDir],
            )),
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())->mapWithKeys(fn ($option) => [$option->value => $option->key])->all(),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }
}
