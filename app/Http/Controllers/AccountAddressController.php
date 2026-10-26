<?php

namespace App\Http\Controllers;

use App\DTO\CustomerAddress\CustomerAddressCreateData;
use App\DTO\CustomerAddress\CustomerAddressUpdateData;
use App\Http\Requests\StoreCustomerAddressRequest;
use App\Http\Requests\UpdateCustomerAddressRequest;
use App\Models\User;
use App\Services\Contracts\CustomerAddressServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountAddressController extends Controller
{
    public function __construct(private readonly CustomerAddressServiceInterface $addresses)
    {
    }

    public function index(): View
    {
        return view('account.addresses.index');
    }

    public function create(Request $request): View
    {
        return view('account.addresses.create', array_merge(
            $this->addresses->formOptions(),
            ['returnToCheckout' => $request->query('return_to') === 'checkout'],
        ));
    }

    public function store(StoreCustomerAddressRequest $request): RedirectResponse
    {
        $this->addresses->create(
            $this->authenticatedUser($request),
            new CustomerAddressCreateData(
                provinceId: $request->integer('province_id'),
                cityId: $request->integer('city_id'),
                fullAddress: $request->string('full_address')->value(),
                phoneNumber: $request->string('phone_number')->value(),
                recipientName: $request->string('recipient_name')->value(),
                recipientNationalCode: $request->filled('recipient_national_code')
                    ? $request->string('recipient_national_code')->value()
                    : null,
            ),
        );

        return redirect()
            ->route($request->query('return_to') === 'checkout' ? 'checkout.show' : 'account.addresses.index')
            ->with('success', 'نشانی جدید با موفقیت ثبت شد.');
    }

    public function edit(Request $request, int $addressId): View
    {
        return view('account.addresses.edit', array_merge(
            ['address' => $this->addresses->details($this->authenticatedUser($request), $addressId)],
            $this->addresses->formOptions(),
        ));
    }

    public function update(UpdateCustomerAddressRequest $request, int $addressId): RedirectResponse
    {
        $this->addresses->update(
            $this->authenticatedUser($request),
            $addressId,
            new CustomerAddressUpdateData(
                provinceId: $request->has('province_id') ? $request->integer('province_id') : null,
                provinceIdProvided: $request->has('province_id'),
                cityId: $request->has('city_id') ? $request->integer('city_id') : null,
                cityIdProvided: $request->has('city_id'),
                fullAddress: $request->has('full_address') ? $request->string('full_address')->value() : null,
                fullAddressProvided: $request->has('full_address'),
                phoneNumber: $request->has('phone_number') ? $request->string('phone_number')->value() : null,
                phoneNumberProvided: $request->has('phone_number'),
                recipientName: $request->has('recipient_name') ? $request->string('recipient_name')->value() : null,
                recipientNameProvided: $request->has('recipient_name'),
                recipientNationalCode: $request->filled('recipient_national_code')
                    ? $request->string('recipient_national_code')->value()
                    : null,
                recipientNationalCodeProvided: $request->has('recipient_national_code'),
            ),
        );

        return redirect()
            ->route('account.addresses.index')
            ->with('success', 'نشانی با موفقیت به‌روزرسانی شد.');
    }

    private function authenticatedUser(Request $request): User
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        return $user;
    }
}
