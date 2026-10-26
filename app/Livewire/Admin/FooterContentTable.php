<?php

namespace App\Livewire\Admin;

use App\DTO\FooterEntryFilterData;
use App\Enums\GlobalStatusEnum;
use App\Services\Contracts\FooterContentServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class FooterContentTable extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    public string $kind;
    public string $search = '';
    public string $status = '';
    public string $sortBy = 'sort_order';
    public string $sortDir = 'asc';
    public int $perPage = 15;

    protected FooterContentServiceInterface $footer;

    public function boot(FooterContentServiceInterface $footer): void
    {
        $this->footer = $footer;
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }
    public function updatedPerPage(): void { $this->resetPage(); }

    public function sort(string $column): void
    {
        if (! in_array($column, ['id', 'title', 'label', 'sort_order', 'status', 'created_at'], true)) return;
        $this->sortDir = $this->sortBy === $column && $this->sortDir === 'asc' ? 'desc' : 'asc';
        $this->sortBy = $column;
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.admin.footer-content-table', [
            'kindTitle' => $this->footer->kinds()[$this->kind] ?? '',
            'entries' => $this->footer->filter($this->kind, new FooterEntryFilterData(
                search: $this->search,
                status: GlobalStatusEnum::tryFrom($this->status),
                perPage: in_array($this->perPage, [10, 15, 25, 50], true) ? $this->perPage : 15,
                page: $this->getPage(),
                sorts: [$this->sortBy => $this->sortDir],
            )),
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())->mapWithKeys(fn ($item) => [$item->value => $item->key])->all(),
        ]);
    }
}
