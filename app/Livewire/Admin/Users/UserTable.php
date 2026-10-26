<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;

use App\DTO\user\UserFilterData;
use App\Enums\GlobalStatusEnum;
use App\Enums\UserTypeEnum;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class UserTable extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $type = '';

    public string $sortBy = 'id';

    public string $sortDir = 'desc';

    public int $perPage = 15;

    protected UserServiceInterface $userService;

    public function boot(UserServiceInterface $userService): void
    {
        $this->userService = $userService;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedType(): void
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
        if (! in_array($column, ['id', 'first_name', 'phone', 'type', 'status', 'created_at'], true)) {
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
        $this->reset('search', 'status', 'type');
        $this->resetPage();
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        return (int) ($this->search !== '')
            + (int) ($this->status !== '')
            + (int) ($this->type !== '');
    }

    public function render(): View
    {
        return view('livewire.admin.users.user-table', [
            'users' => $this->userService->filter(
                new UserFilterData(
                    search: $this->search,
                    status: GlobalStatusEnum::tryFrom($this->status),
                    type: UserTypeEnum::tryFrom($this->type),
                    perPage: $this->perPage,
                    page: $this->getPage(),
                    sorts: [$this->sortBy => $this->sortDir],
                ),
            ),
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
            'typeOptions' => collect(UserTypeEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }
}
