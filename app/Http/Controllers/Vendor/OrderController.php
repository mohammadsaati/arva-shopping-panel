<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Services\Contracts\VendorPanelServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly VendorPanelServiceInterface $panel)
    {
    }

    public function index(): View
    {
        return view('vendor.orders.index');
    }

    public function show(Request $request, string $vendorOrderId): View
    {
        return view('vendor.orders.show', [
            'vendorOrder' => $this->panel->order($request->user(), (int) $vendorOrderId),
        ]);
    }
}
