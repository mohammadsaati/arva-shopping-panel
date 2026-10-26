<?php

namespace App\Services\Contracts;

use App\DTO\Storefront\CatalogFilterData;
use App\DTO\Storefront\ItemSuggestData;
use App\DTO\Storefront\SectionItemsFilterData;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Item;
use App\Models\Product;
use App\Models\Section;
use App\Models\Slider;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;

interface StorefrontServiceInterface
{
    /** @return Collection<int, Category> */
    public function homeCategories(): Collection;

    /** @return Collection<int, Category> */
    public function navigationCategories(): Collection;

    /** @return Collection<int, Product> */
    public function featuredProducts(?string $search = null): Collection;

    public function catalogItems(CatalogFilterData $filters): Paginator;

    /** @return Collection<int, Item> */
    public function suggestItems(ItemSuggestData $data): Collection;

    public function categoryDetails(string $slug): Category;

    public function sectionDetails(string $slug): Section;

    public function sectionItems(SectionItemsFilterData $filters): Paginator;

    /** @return array<int, string> */
    public function catalogBrands(): array;

    public function productDetails(int $productId): Product;

    /** @return Collection<int, Product> */
    public function relatedProducts(int $categoryId, int $excludedItemId): Collection;

    /** @param array<int, int> $visibleSectionIds
     *  @return Collection<int, Slider>
     */
    public function homeSliders(array $visibleSectionIds): Collection;

    /** @return Collection<int, Banner> */
    public function homeBanners(): Collection;

    /** @return Collection<int, Item> */
    public function amazingItems(): Collection;

    /** @return Collection<int, Section> */
    public function homeSections(): Collection;
}
