<?php

namespace App\Livewire\Admin\Discounts;

use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;

use App\DTO\Discount\DiscountTargetFilterData;
use App\Services\Contracts\DiscountServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class DiscountTargetPicker extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    public string $targetType = 'products';

    public string $search = '';

    public bool $selectedOnly = false;

    public int $perPage = 10;

    /** @var array<int, int> */
    public array $selectedProductIds = [];

    /** @var array<int, int> */
    public array $selectedCategoryIds = [];

    public ?int $selectedUserId = null;

    public ?string $selectionError = null;

    protected DiscountServiceInterface $discounts;

    public function boot(DiscountServiceInterface $discounts): void
    {
        $this->discounts = $discounts;
    }

    /** @param array<int, int|string> $selectedProductIds @param array<int, int|string> $selectedCategoryIds */
    public function mount(array $selectedProductIds = [], array $selectedCategoryIds = [], ?int $selectedUserId = null): void
    {
        $this->selectedProductIds = $this->normalizeIds($selectedProductIds);
        $this->selectedCategoryIds = $this->normalizeIds($selectedCategoryIds);
        $this->selectedUserId = $selectedUserId !== null && $selectedUserId > 0 ? $selectedUserId : null;
    }

    public function showProducts(): void
    {
        $this->switchTargetType('products');
    }

    public function showCategories(): void
    {
        $this->switchTargetType('categories');
    }

    public function showUsers(): void
    {
        $this->switchTargetType('users');
    }

    public function clearUserSelection(): void
    {
        $this->selectedUserId = null;
        $this->selectionError = null;
    }

    public function updatedTargetType(): void
    {
        if (! in_array($this->targetType, ['products', 'categories', 'users'], true)) {
            $this->targetType = 'products';
        }

        $this->resetPage();
    }

    public function updatedSearch(): void
    {
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
        $this->resetPage();
    }

    public function activeFilterCount(): int
    {
        $count = 0;

        if ($this->search !== '') {
            $count++;
        }

        if ($this->selectedOnly) {
            $count++;
        }

        return $count;
    }

    public function toggleTarget(int $targetId): void
    {
        $this->selectionError = null;

        if (! in_array($this->targetType, ['products', 'categories', 'users'], true) || $targetId < 1) {
            return;
        }

        if ($this->targetType === 'users') {
            if ($this->selectedUserId === $targetId) {
                $this->selectedUserId = null;

                return;
            }

            if (! $this->discounts->targetExists('users', $targetId)) {
                $this->selectionError = 'این کاربر برای انتخاب در دسترس نیست.';

                return;
            }

            $this->selectedUserId = $targetId;

            return;
        }

        $selectedIds = $this->normalizeIds($this->selectedIdsForCurrentType());

        if (in_array($targetId, $selectedIds, true)) {
            $selectedIds = array_values(array_diff($selectedIds, [$targetId]));
            $this->setSelectedIdsForCurrentType($selectedIds);

            return;
        }

        $limit = $this->targetType === 'products' ? 1000 : 500;

        if (count($selectedIds) >= $limit) {
            $this->selectionError = "حداکثر {$limit} مورد قابل انتخاب است.";

            return;
        }

        if (! $this->discounts->targetExists($this->targetType, $targetId)) {
            $this->selectionError = 'این مورد برای انتخاب در دسترس نیست.';

            return;
        }

        $selectedIds[] = $targetId;
        $this->setSelectedIdsForCurrentType($selectedIds);
    }

    public function render(): View
    {
        if (! in_array($this->targetType, ['products', 'categories', 'users'], true)) {
            $this->targetType = 'products';
        }

        $this->selectedProductIds = array_slice($this->normalizeIds($this->selectedProductIds), 0, 1000);
        $this->selectedCategoryIds = array_slice($this->normalizeIds($this->selectedCategoryIds), 0, 500);
        $this->selectedUserId = $this->selectedUserId !== null && $this->selectedUserId > 0
            ? $this->selectedUserId
            : null;
        $selectedIds = $this->selectedIdsForCurrentType();

        return view('livewire.admin.discounts.discount-target-picker', [
            'targets' => $this->discounts->selectableTargets(new DiscountTargetFilterData(
                targetType: $this->targetType,
                search: trim($this->search),
                selectedIds: $selectedIds,
                selectedOnly: $this->selectedOnly,
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: $this->targetType === 'categories' ? ['name' => 'asc'] : ['id' => 'desc'],
            )),
            'selectedIds' => $selectedIds,
        ]);
    }

    /** @param array<int, int|string> $ids @return array<int, int> */
    private function normalizeIds(array $ids): array
    {
        return array_values(array_unique(array_filter(
            array_map('intval', $ids),
            static fn (int $id): bool => $id > 0,
        )));
    }

    /** @return array<int, int> */
    private function selectedIdsForCurrentType(): array
    {
        return match ($this->targetType) {
            'categories' => $this->selectedCategoryIds,
            'users' => $this->selectedUserId !== null ? [$this->selectedUserId] : [],
            default => $this->selectedProductIds,
        };
    }

    /** @param array<int, int> $ids */
    private function setSelectedIdsForCurrentType(array $ids): void
    {
        if ($this->targetType === 'categories') {
            $this->selectedCategoryIds = $ids;

            return;
        }

        if ($this->targetType === 'users') {
            $this->selectedUserId = $ids[0] ?? null;

            return;
        }

        $this->selectedProductIds = $ids;
    }

    private function switchTargetType(string $targetType): void
    {
        $this->targetType = $targetType;
        $this->search = '';
        $this->selectedOnly = false;
        $this->selectionError = null;
        $this->resetPage();
    }
}
