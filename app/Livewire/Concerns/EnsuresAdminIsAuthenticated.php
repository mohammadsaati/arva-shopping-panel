<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Auth;

trait EnsuresAdminIsAuthenticated
{
    public function bootEnsuresAdminIsAuthenticated(): void
    {
        abort_unless(Auth::guard('admin')->check(), 403);
    }
}
