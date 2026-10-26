<?php

namespace App\Livewire\Storefront\Account;

use App\DTO\CustomerAddress\CustomerAddressFilterData;
use App\Models\User;
use App\Services\Contracts\CustomerAddressServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class AddressTable extends Component
{
    use WithPagination;

    public int $perPage = 10;

    protected CustomerAddressServiceInterface $addresses;

    public function boot(CustomerAddressServiceInterface $addresses): void
    {
        $this->addresses = $addresses;
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

        return view('livewire.storefront.account.address-table', [
            'addresses' => $this->addresses->filter($user, new CustomerAddressFilterData(
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: ['id' => 'desc'],
            )),
            'perPageOptions' => [10, 15, 25, 50],
        ]);
    }
}
