<?php

namespace App\Http\Controllers;

use App\DTO\Storefront\ItemSuggestData;
use App\Http\Requests\SuggestItemsRequest;
use App\Models\Item;
use App\Services\Contracts\StorefrontServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class ShopController extends Controller
{
    public function __construct(private readonly StorefrontServiceInterface $storefrontService) {}

    public function index(): View
    {
        return view('shop.index');
    }

    public function suggest(SuggestItemsRequest $request): JsonResponse
    {
        $items = $this->storefrontService->suggestItems(new ItemSuggestData(
            term: $request->string('q')->value(),
        ));

        return response()->json([
            'items' => $items->map(function (Item $item): array {
                $product = $item->products->first();

                return [
                    'name' => $item->name,
                    'brand' => $item->brand?->name,
                    'category' => $item->category?->name,
                    'image' => public_image_url($item->image),
                    'url' => $product !== null
                        ? route('shop.products.show', ['productId' => $product->id])
                        : route('shop.index', ['search' => $item->name]),
                    'price' => $product?->price,
                ];
            })->values(),
        ]);
    }

    public function show(int $productId): View
    {
        $product = $this->storefrontService->productDetails($productId);

        return view('shop.show', [
            'product' => $product,
            'relatedProducts' => $this->storefrontService->relatedProducts(
                categoryId: $product->item->category_id,
                excludedItemId: $product->item_id,
            ),
        ]);
    }

    public function category(string $slug): View
    {
        return view('categories.show', [
            'category' => $this->storefrontService->categoryDetails($slug),
        ]);
    }

    public function section(string $slug): View
    {
        return view('sections.show', [
            'section' => $this->storefrontService->sectionDetails($slug),
        ]);
    }
}
