<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use App\Services\Contracts\VendorServiceInterface;
use Illuminate\Support\Facades\Auth;

trait EnsuresVendorIsAuthenticated
{
    public function bootEnsuresVendorIsAuthenticated(): void
    {
        $user = Auth::user();
        abort_unless($user instanceof User, 403);
        app(VendorServiceInterface::class)->profileForUser($user);
    }
}
