<?php

namespace App\Livewire\Admin\Orders;

use App\DTO\Order\OrderFilterData;
use App\DTO\Order\OrderStatusUpdateData;
use App\Enums\OrderStatusEnum;
use App\Enums\PaymentStatusEnum;
use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;
use App\Services\Contracts\OrderServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithPagination;

class OrderTable extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    #[Locked]
    public ?int $userId = null;

    public string $search = '';
    public string $status = '';
    public string $paymentStatus = '';
    public string $sortBy = 'id';
    public string $sortDir = 'desc';
    public int $perPage = 15;

    protected OrderServiceInterface $orders;

    public function boot(OrderServiceInterface $orders): void
    {
        $this->orders = $orders;
    }

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }
    public function updatedPaymentStatus(): void { $this->resetPage(); }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, [10, 15, 25, 50], true)) {
            $this->perPage = 15;
        }

        $this->resetPage();
    }

    public function sort(string $column): void
    {
        if (! in_array($column, ['id', 'amount', 'status', 'payment_status', 'created_at'], true)) {
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
        $this->reset('search', 'status', 'paymentStatus');
        $this->resetPage();
    }

    public function updateStatus(int $orderId, string $status): void
    {
        $orderStatus = OrderStatusEnum::tryFrom($status);

        if ($orderStatus === null) {
            $this->addError('orderStatus', 'وضعیت سفارش انتخاب‌شده معتبر نیست.');

            return;
        }

        $this->resetErrorBag('orderStatus');
        $this->orders->updateStatus($orderId, new OrderStatusUpdateData(status: $orderStatus));
        $this->dispatch('menu-badge-updated', key: 'admin.orders.index', value: $this->orders->pendingCount());
        session()->flash('success', "وضعیت سفارش #{$orderId} به‌روزرسانی شد.");
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        return (int) ($this->search !== '')
            + (int) ($this->status !== '')
            + (int) ($this->paymentStatus !== '');
    }

    public function render(): View
    {
        return view('livewire.admin.orders.order-table', [
            'orders' => $this->orders->filterForAdmin(new OrderFilterData(
                search: $this->search,
                status: OrderStatusEnum::tryFrom($this->status),
                paymentStatus: PaymentStatusEnum::tryFrom($this->paymentStatus),
                userId: $this->userId,
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: [$this->sortBy => $this->sortDir],
            )),
            'statusOptions' => collect(OrderStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
            'paymentStatusOptions' => collect(PaymentStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }
}
