@props([
    'title' => null,
    'description' => null,
    'categories' => null,
    'chrome' => true,
])

@extends('layouts.user')

@section('content')
    @if ($chrome)
        <x-user.header />
    @endif

    <main id="main-content" tabindex="-1" @class(['user-auth-main' => ! $chrome])>
        @if ($chrome && $errors->has('cart'))
            <div class="user-global-cart-error" role="alert">{{ $errors->first('cart') }}</div>
        @endif
        {{ $slot }}
    </main>

    @if ($chrome)
        <x-user.footer />
    @endif
@endsection
