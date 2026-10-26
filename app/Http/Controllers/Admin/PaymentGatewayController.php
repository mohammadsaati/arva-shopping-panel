<?php

namespace App\Http\Controllers\Admin;

use App\DTO\Payment\PaymentGatewayUpdateData;
use App\Enums\GlobalStatusEnum;
use App\Enums\PaymentCurrencyEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAdminPaymentGatewayRequest;
use App\Services\Contracts\PaymentGatewayServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentGatewayController extends Controller
{
    public function __construct(private readonly PaymentGatewayServiceInterface $gateways)
    {
    }

    public function index(): View
    {
        return view('admin.payment-gateways.index');
    }

    public function edit(int $gatewayId): View
    {
        $gateway = $this->gateways->details($gatewayId);

        return view('admin.payment-gateways.edit', [
            'gateway' => $gateway,
            'statusOptions' => $this->options(GlobalStatusEnum::toKeyValueType()),
            'currencyOptions' => $this->options(PaymentCurrencyEnum::toKeyValueType()),
        ]);
    }

    public function update(UpdateAdminPaymentGatewayRequest $request, int $gatewayId): RedirectResponse
    {
        $gateway = $this->gateways->update($gatewayId, new PaymentGatewayUpdateData(
            name: $request->string('name')->value(),
            status: $request->enum('status', GlobalStatusEnum::class),
            merchantId: $request->filled('merchant_id') ? $request->string('merchant_id')->value() : '',
            merchantIdProvided: true,
            sandbox: $request->boolean('sandbox'),
            sandboxProvided: true,
            currency: $request->enum('currency', PaymentCurrencyEnum::class),
            currencyProvided: true,
            image: $request->file('image'),
            removeImage: $request->boolean('remove_image'),
        ));

        return redirect()
            ->route('admin.payment-gateways.edit', $gateway->id)
            ->with('success', 'تنظیمات درگاه «'.$gateway->name.'» ذخیره شد.');
    }

    public function toggle(int $gatewayId): RedirectResponse
    {
        $gateway = $this->gateways->toggleStatus($gatewayId);
        $state = $gateway->status === GlobalStatusEnum::ACTIVE ? 'فعال' : 'غیرفعال';

        return redirect()
            ->route('admin.payment-gateways.index')
            ->with('success', 'درگاه «'.$gateway->name.'» '.$state.' شد.');
    }

    /**
     * @param array<int, \App\DTO\KeyValueType> $values
     * @return array<string, string>
     */
    private function options(array $values): array
    {
        return collect($values)->mapWithKeys(fn ($item) => [$item->value => $item->key])->all();
    }
}
