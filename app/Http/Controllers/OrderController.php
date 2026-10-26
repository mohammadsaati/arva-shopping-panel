<?php

namespace App\Http\Controllers;

use App\Services\Contracts\OrderServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly OrderServiceInterface $orders) {}

    public function index(): View
    {
        return view('account.orders.index');
    }

    public function show(Request $request, int $orderId): View
    {
        return view('orders.show', [
            'order' => $this->orders->details($request->user(), $orderId),
        ]);
    }
}
