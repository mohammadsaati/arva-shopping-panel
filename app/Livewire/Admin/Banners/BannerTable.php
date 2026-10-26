<?php

namespace App\Livewire\Admin\Banners;

use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;

use App\DTO\Banner\BannerFilterData;
use App\Enums\BannerTypeEnum;
use App\Enums\GlobalStatusEnum;
use App\Services\Contracts\BannerServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class BannerTable extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    public string $search = '';
    public string $type = '';
    public string $status = '';
    public string $sortBy = 'id';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    protected BannerServiceInterface $banners;

    public function boot(BannerServiceInterface $banners): void
    {
        $this->banners = $banners;
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedType(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, [10, 15, 25, 50], true)) {
            $this->perPage = 15;
        }
        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if (! in_array($column, ['id', 'type', 'status', 'created_at'], true)) return;
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
        $this->reset('search', 'type', 'status');
        $this->resetPage();
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        return (int) ($this->search !== '') + (int) ($this->type !== '') + (int) ($this->status !== '');
    }

    public function render(): View
    {
        return view('livewire.admin.banners.banner-table', [
            'banners' => $this->banners->filter(new BannerFilterData(
                search: $this->search,
                type: BannerTypeEnum::tryFrom($this->type),
                status: GlobalStatusEnum::tryFrom($this->status),
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: [$this->sortBy => $this->sortDir],
            )),
            'typeOptions' => $this->options(BannerTypeEnum::toKeyValueType()),
            'statusOptions' => $this->options(GlobalStatusEnum::toKeyValueType()),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }

    private function options(array $values): array
    {
        return collect($values)->mapWithKeys(fn ($item) => [$item->value => $item->key])->all();
    }
}
