<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\Contracts\VendorPanelServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private readonly VendorPanelServiceInterface $panel)
    {
    }

    public function index(Request $request): View
    {
        return view('vendor.dashboard', ['counts' => $this->panel->dashboard($request->user())]);
    }
}
