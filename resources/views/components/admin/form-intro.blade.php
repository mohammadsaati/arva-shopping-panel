@props(['title', 'description', 'icon' => 'ki-filled ki-setting-2'])

<div class="admin-form-intro">
    <span class="admin-form-intro__icon"><i class="{{ $icon }}" aria-hidden="true"></i></span>
    <div><strong>{{ $title }}</strong><p>{{ $description }}</p></div>
</div>
