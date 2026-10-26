@props(['label' => 'عملیات ردیف'])

<div
    class="dropdown relative inline-flex"
    data-dropdown="true"
    data-dropdown-trigger="click"
    data-dropdown-placement="bottom-end"
>
    <button
        type="button"
        class="dropdown-toggle btn btn-sm btn-icon btn-light"
        data-dropdown-toggle="true"
        aria-label="{{ $label }}"
        aria-haspopup="menu"
        aria-expanded="false"
    >
        <i class="ki-filled ki-dots-vertical" aria-hidden="true"></i>
    </button>

    <div class="dropdown-content w-44 py-1" data-dropdown-content="true" role="menu">
        <div class="menu menu-default flex w-full flex-col">
            {{ $slot }}
        </div>
    </div>
</div>
