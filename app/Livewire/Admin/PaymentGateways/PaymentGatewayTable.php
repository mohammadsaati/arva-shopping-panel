<?php

namespace App\Livewire\Admin\PaymentGateways;

use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;

use App\DTO\Payment\PaymentGatewayFilterData;
use App\Enums\GlobalStatusEnum;
use App\Services\Contracts\PaymentGatewayServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentGatewayTable extends Component
{
    use EnsuresAdminIsAuthenticated;

    use WithPagination;

    public string $search = '';

    public string $status = '';

    public string $sortBy = 'id';

    public string $sortDir = 'asc';

    public int $perPage = 15;

    protected PaymentGatewayServiceInterface $gateways;

    public function boot(PaymentGatewayServiceInterface $gateways): void
    {
        $this->gateways = $gateways;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
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
        if (! in_array($column, ['id', 'name', 'driver', 'status', 'created_at'], true)) {
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
        $this->reset('search', 'status');
        $this->resetPage();
    }

    public function toggleStatus(int $gatewayId): void
    {
        try {
            $gateway = $this->gateways->toggleStatus($gatewayId);
            $state = $gateway->status === GlobalStatusEnum::ACTIVE ? 'فعال' : 'غیرفعال';
            session()->flash('success', 'درگاه «'.$gateway->name.'» '.$state.' شد.');
        } catch (ValidationException $exception) {
            $this->addError('status', $exception->errors()['merchant_id'][0] ?? $exception->errors()['status'][0] ?? 'تغییر وضعیت ممکن نیست.');
        }
    }

    #[Computed]
    public function activeFilterCount(): int
    {
        return (int) ($this->search !== '') + (int) ($this->status !== '');
    }

    public function render(): View
    {
        return view('livewire.admin.payment-gateways.payment-gateway-table', [
            'gateways' => $this->gateways->filter(new PaymentGatewayFilterData(
                search: $this->search,
                status: GlobalStatusEnum::tryFrom($this->status),
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: [$this->sortBy => $this->sortDir],
            )),
            'statusOptions' => collect(GlobalStatusEnum::toKeyValueType())
                ->mapWithKeys(fn ($item) => [$item->value => $item->key])
                ->all(),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }
}
