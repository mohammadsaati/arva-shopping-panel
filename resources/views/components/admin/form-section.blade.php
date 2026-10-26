@props(['title', 'description' => null, 'icon' => 'ki-filled ki-setting-2', 'compact' => false, 'twoColumns' => false])

<section {{ $attributes->class(['admin-form-section', 'admin-form-section--compact' => $compact]) }}>
    <div class="admin-form-section__heading">
        <span class="admin-form-section__icon"><i class="{{ $icon }}" aria-hidden="true"></i></span>
        <div>
            <h2>{{ $title }}</h2>
            @if ($description)<p>{{ $description }}</p>@endif
        </div>
    </div>
    <div @class(['admin-form-section__fields', 'admin-form-section__fields--two' => $twoColumns])>{{ $slot }}</div>
</section>
