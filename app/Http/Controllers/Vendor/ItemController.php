<?php

namespace App\Http\Controllers\Vendor;

use App\DTO\VendorPanel\VendorItemCreateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVendorItemRequest;
use App\Services\Contracts\VendorPanelServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function __construct(private readonly VendorPanelServiceInterface $panel)
    {
    }

    public function index(): View
    {
        return view('vendor.items.index');
    }

    public function create(): View
    {
        return view('vendor.items.create', $this->panel->itemFormOptions());
    }

    public function store(StoreVendorItemRequest $request): RedirectResponse
    {
        $item = $this->panel->createItem($request->user(), new VendorItemCreateData(
            name: $request->string('name')->value(),
            slug: $request->string('slug')->value(),
            code: $request->integer('code'),
            categoryId: $request->integer('category_id'),
            brandId: $request->filled('brand_id') ? $request->integer('brand_id') : null,
            image: $request->file('image'),
            description: $request->filled('description') ? $request->string('description')->value() : null,
        ));

        return redirect()->route('vendor.items.show', $item->id)->with('success', 'آیتم ثبت شد.');
    }

    public function show(Request $request, string $itemId): View
    {
        return view('vendor.items.show', ['item' => $this->panel->item($request->user(), (int) $itemId)]);
    }
}
