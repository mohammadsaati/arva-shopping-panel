<?php

namespace App\Livewire\Admin\Dashboard;

use App\Livewire\Concerns\EnsuresAdminIsAuthenticated;

use App\Services\Contracts\DashboardServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Overview extends Component
{
    use EnsuresAdminIsAuthenticated;

    protected DashboardServiceInterface $dashboard;

    public function boot(DashboardServiceInterface $dashboard): void
    {
        $this->dashboard = $dashboard;
    }

    public function render(): View
    {
        return view('livewire.admin.dashboard.overview', [
            'overview' => $this->dashboard->overview(),
        ]);
    }
}
