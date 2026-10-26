<?php

namespace App\Livewire\Admin\Sliders;

use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;

use App\DTO\Slider\SliderFilterData;
use App\Enums\GlobalStatusEnum;
use App\Enums\SliderTypeEnum;
use App\Services\Contracts\SliderServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class SliderTable extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    public string $search = '';
    public string $type = '';
    public string $status = '';
    public string $sortBy = 'priority';
    public string $sortDir = 'asc';
    public int $perPage = 15;

    protected SliderServiceInterface $sliders;

    public function boot(SliderServiceInterface $sliders): void
    {
        $this->sliders = $sliders;
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
        if (! in_array($column, ['id', 'title', 'priority', 'type', 'status', 'created_at'], true)) return;
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
        return view('livewire.admin.sliders.slider-table', [
            'sliders' => $this->sliders->filter(new SliderFilterData(
                search: $this->search,
                type: SliderTypeEnum::tryFrom($this->type),
                status: GlobalStatusEnum::tryFrom($this->status),
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: [$this->sortBy => $this->sortDir],
            )),
            'typeOptions' => $this->options(SliderTypeEnum::toKeyValueType()),
            'statusOptions' => $this->options(GlobalStatusEnum::toKeyValueType()),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }

    private function options(array $values): array
    {
        return collect($values)->mapWithKeys(fn ($item) => [$item->value => $item->key])->all();
    }
}
