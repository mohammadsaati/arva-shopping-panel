<?php

namespace App\Livewire\Admin;

use App\DTO\NewsletterSubscriberFilterData;
use App\Enums\GlobalStatusEnum;
use App\Services\Contracts\NewsletterServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class NewsletterSubscriberTable extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $sortBy = 'subscribed_at';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    protected NewsletterServiceInterface $newsletter;

    public function boot(NewsletterServiceInterface $newsletter): void
    {
        $this->newsletter = $newsletter;
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }
    public function updatedPerPage(): void { $this->resetPage(); }

    public function sort(string $column): void
    {
        if (! in_array($column, ['id', 'email', 'status', 'subscribed_at'], true)) return;
        $this->sortDir = $this->sortBy === $column && $this->sortDir === 'asc' ? 'desc' : 'asc';
        $this->sortBy = $column;
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.admin.newsletter-subscriber-table', [
            'subscribers' => $this->newsletter->subscribers(new NewsletterSubscriberFilterData(
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
