<?php

namespace App\Livewire\Storefront\Account;

use App\DTO\Order\OrderHistoryFilterData;
use App\Enums\OrderStatusEnum;
use App\Models\User;
use App\Services\Contracts\OrderServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class OrderTable extends Component
{
    use WithPagination;

    public string $status = '';
    public string $search = '';
    public int $perPage = 10;

    protected OrderServiceInterface $orders;

    public function boot(OrderServiceInterface $orders): void
    {
        $this->orders = $orders;
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setStatus(string $status): void
    {
        if ($status !== '' && OrderStatusEnum::tryFrom($status) === null) {
            return;
        }

        $this->status = $status;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->status = '';
        $this->search = '';
        $this->resetPage();
    }

    public function clearSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, [10, 15, 25, 50], true)) {
            $this->perPage = 10;
        }

        $this->resetPage();
    }

    public function render(): View
    {
        $user = Auth::guard('web')->user();
        abort_unless($user instanceof User, 403);

        return view('livewire.storefront.account.order-table', [
            'orders' => $this->orders->history($user, new OrderHistoryFilterData(
                status: OrderStatusEnum::tryFrom($this->status),
                search: $this->search !== '' ? $this->search : null,
                perPage: $this->perPage,
                page: $this->getPage(),
            )),
            'statusOptions' => collect(OrderStatusEnum::toKeyValueType())
                ->map(fn ($option) => [
                    'value' => $option->value,
                    'label' => $option->key,
                    'color' => OrderStatusEnum::from($option->value)->color(),
                ])
                ->all(),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }
}
