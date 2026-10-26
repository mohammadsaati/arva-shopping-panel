@props(['order', 'options', 'wireId' => null])

<div
    class="dropdown ui-order-status-picker relative inline-flex"
    data-dropdown="true"
    data-dropdown-trigger="click"
    data-dropdown-placement="bottom-end"
>
    <button
        type="button"
        class="dropdown-toggle ui-order-status-picker__trigger"
        data-dropdown-toggle="true"
        wire:loading.attr="disabled"
        wire:target="updateStatus"
        aria-label="وضعیت سفارش شماره {{ $order->id }}: {{ $order->status->trans() }}. تغییر وضعیت"
        aria-haspopup="menu"
        aria-expanded="false"
    >
        <x-badge :variant="$order->status->color()" size="sm" dot>{{ $order->status->trans() }}</x-badge>
        <i class="ki-filled ki-down text-xs" aria-hidden="true"></i>
    </button>

    <div class="dropdown-content ui-order-status-picker__menu" data-dropdown-content="true" role="menu" aria-label="وضعیت‌های سفارش شماره {{ $order->id }}">
        <p class="ui-order-status-picker__heading">تغییر وضعیت به</p>
        @foreach ($options as $value => $label)
            @php($optionStatus = \App\Enums\OrderStatusEnum::from($value))
            <button
                type="button"
                class="ui-order-status-picker__option"
                role="menuitem"
                data-dropdown-dismiss="true"
                wire:loading.attr="disabled"
                wire:target="updateStatus"
                @if ($order->status === $optionStatus)
                    disabled
                    aria-current="true"
                @else
                    data-confirm-open="order-status-confirm"
                    data-confirm-title="تأیید تغییر وضعیت"
                    data-confirm-message="وضعیت سفارش #{{ $order->id }} به «:selection» تغییر کند؟"
                    data-confirm-text="تغییر وضعیت"
                    data-confirm-livewire="updateStatus"
                    @if ($wireId) data-confirm-wire-id="{{ $wireId }}" @endif
                    data-confirm-args="[{{ $order->id }}]"
                    data-confirm-value="{{ $value }}"
                    data-confirm-selection="{{ $label }}"
                @endif
            >
                <x-badge :variant="$optionStatus->color()" size="sm" dot>{{ $label }}</x-badge>
                @if ($order->status === $optionStatus)
                    <i class="ki-filled ki-check text-success" aria-hidden="true"></i>
                @endif
            </button>
        @endforeach
    </div>
</div>
