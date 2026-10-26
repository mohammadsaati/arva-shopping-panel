<?php

namespace App\Livewire\Storefront;

use App\DTO\Storefront\SectionItemsFilterData;
use App\Services\Contracts\StorefrontServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SectionItems extends Component
{
    use WithPagination;

    #[Locked]
    public int $sectionId;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: 'latest')]
    public string $sort = 'latest';

    public int $perPage = 12;

    protected StorefrontServiceInterface $storefrontService;

    public function boot(StorefrontServiceInterface $storefrontService): void
    {
        $this->storefrontService = $storefrontService;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, [12, 24, 36], true)) {
            $this->perPage = 12;
        }

        $this->resetPage();
    }

    public function setSort(string $sort): void
    {
        $this->sort = in_array($sort, ['latest', 'name_asc'], true) ? $sort : 'latest';
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function render(): View
    {
        return view('livewire.storefront.section-items', [
            'items' => $this->storefrontService->sectionItems(new SectionItemsFilterData(
                sectionId: $this->sectionId,
                search: trim($this->search) !== '' ? trim($this->search) : null,
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: $this->sort === 'name_asc' ? ['name' => 'asc'] : ['id' => 'desc'],
            )),
        ]);
    }
}
