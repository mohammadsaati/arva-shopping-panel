<?php

namespace App\Livewire\Admin\Sections;

use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;

use App\DTO\Section\SectionItemFilterData;
use App\Models\Item;
use App\Services\Contracts\SectionServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class SectionItemPicker extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    public string $search = '';

    public bool $selectedOnly = false;

    public string $homeAvailability = '';

    public int $perPage = 10;

    /** @var array<int, int> */
    public array $selectedItemIds = [];

    public ?string $selectionError = null;

    protected SectionServiceInterface $sections;

    public function boot(SectionServiceInterface $sections): void
    {
        $this->sections = $sections;
    }

    /** @param array<int, int|string> $selectedItemIds */
    public function mount(array $selectedItemIds = []): void
    {
        $this->selectedItemIds = array_values(array_unique(array_filter(
            array_map('intval', $selectedItemIds),
            fn (int $id): bool => $id > 0,
        )));
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedHomeAvailability(): void
    {
        if (! in_array($this->homeAvailability, ['', 'available', 'unavailable'], true)) {
            $this->homeAvailability = '';
        }

        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, [10, 15, 25, 50], true)) {
            $this->perPage = 10;
        }

        $this->resetPage();
    }

    public function toggleSelectedOnly(): void
    {
        $this->selectedOnly = ! $this->selectedOnly;
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->search = '';
        $this->selectedOnly = false;
        $this->homeAvailability = '';
        $this->resetPage();
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        $count = 0;

        if ($this->search !== '') {
            $count++;
        }

        if ($this->homeAvailability !== '') {
            $count++;
        }

        if ($this->selectedOnly) {
            $count++;
        }

        return $count;
    }

    public function selectVisibleItems(): void
    {
        $this->selectionError = null;
        $eligibleIds = [];

        foreach ($this->visibleItems() as $item) {
            if ($this->sections->homeAvailabilityForItem($item)['available']) {
                $eligibleIds[] = $item->id;
            }
        }

        $newIds = array_values(array_diff($eligibleIds, $this->selectedItemIds));

        if ($newIds === []) {
            if ($eligibleIds === []) {
                $this->selectionError = 'در این صفحه آیتم قابل نمایش برای انتخاب وجود ندارد.';
            }

            return;
        }

        $availableSlots = max(0, 100 - count($this->selectedItemIds));
        $selectedIds = array_slice($newIds, 0, $availableSlots);
        $this->selectedItemIds = array_values(array_unique([...$this->selectedItemIds, ...$selectedIds]));

        if (count($selectedIds) < count($newIds)) {
            $this->selectionError = 'حداکثر ۱۰۰ آیتم قابل انتخاب است؛ بخشی از آیتم‌های این صفحه انتخاب نشدند.';
        }
    }

    public function clearVisibleItems(): void
    {
        $visibleIds = array_map(
            static fn (Item $item): int => $item->id,
            $this->visibleItems(),
        );

        $this->selectedItemIds = array_values(array_diff($this->selectedItemIds, $visibleIds));
        $this->selectionError = null;
    }

    public function toggleItem(int $itemId): void
    {
        $this->selectionError = null;

        if (in_array($itemId, $this->selectedItemIds, true)) {
            $this->selectedItemIds = array_values(array_diff($this->selectedItemIds, [$itemId]));

            return;
        }

        if (count($this->selectedItemIds) >= 100) {
            $this->selectionError = 'حداکثر ۱۰۰ آیتم قابل انتخاب است.';

            return;
        }

        if (! $this->sections->canSelectItem($itemId)) {
            $this->selectionError = 'این آیتم برای انتخاب در دسترس نیست.';

            return;
        }

        $this->selectedItemIds[] = $itemId;
    }

    public function render(): View
    {
        $items = $this->filteredItems();

        return view('livewire.admin.sections.section-item-picker', [
            'items' => $items,
            'itemHomeAvailability' => collect($items->items())
                ->mapWithKeys(fn (Item $item) => [$item->id => $this->sections->homeAvailabilityForItem($item)])
                ->all(),
        ]);
    }

    /** @return array<int, Item> */
    private function visibleItems(): array
    {
        return $this->filteredItems()->items();
    }

    private function filteredItems(): Paginator
    {
        return $this->sections->selectableItems(new SectionItemFilterData(
            search: trim($this->search),
            selectedItemIds: $this->selectedItemIds,
            selectedOnly: $this->selectedOnly,
            homeAvailable: match ($this->homeAvailability) {
                'available' => true,
                'unavailable' => false,
                default => null,
            },
            perPage: $this->perPage,
            page: $this->getPage(),
        ));
    }
}
