<?php

namespace App\Services;

use App\DTO\Storefront\CatalogFilterData;
use App\DTO\Storefront\ItemSuggestData;
use App\DTO\Storefront\SectionItemsFilterData;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Item;
use App\Models\Product;
use App\Models\Section;
use App\Models\Slider;
use App\Services\Contracts\StorefrontServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\Paginator;

class StorefrontService implements StorefrontServiceInterface
{
    /** @return Collection<int, Category> */
    public function homeCategories(): Collection
    {
        return Category::homePageCategories();
    }

    /** @return Collection<int, Category> */
    public function navigationCategories(): Collection
    {
        return Category::activeNavigationTree();
    }

    /** @return Collection<int, Product> */
    public function featuredProducts(?string $search = null): Collection
    {
        return Product::featuredForHome(search: $search);
    }

    public function catalogItems(CatalogFilterData $filters): Paginator
    {
        return Item::catalog($filters);
    }

    /** @return Collection<int, Item> */
    public function suggestItems(ItemSuggestData $data): Collection
    {
        return Item::suggestForSearch($data);
    }

    public function categoryDetails(string $slug): Category
    {
        return Category::publicDetailsBySlug($slug);
    }

    public function sectionDetails(string $slug): Section
    {
        return Section::publicDetailsBySlug($slug);
    }

    public function sectionItems(SectionItemsFilterData $filters): Paginator
    {
        return Item::publicSectionItems($filters);
    }

    /** @return array<int, string> */
    public function catalogBrands(): array
    {
        return Brand::activeOptions();
    }

    public function productDetails(int $productId): Product
    {
        return Product::storefrontDetails($productId);
    }

    /** @return Collection<int, Product> */
    public function relatedProducts(int $categoryId, int $excludedItemId): Collection
    {
        return Item::relatedProducts($categoryId, $excludedItemId);
    }

    /** @param array<int, int> $visibleSectionIds
     *  @return Collection<int, Slider>
     */
    public function homeSliders(array $visibleSectionIds): Collection
    {
        return Slider::forHome($visibleSectionIds);
    }

    /** @return Collection<int, Banner> */
    public function homeBanners(): Collection
    {
        return Banner::forHome();
    }

    /** @return Collection<int, Item> */
    public function amazingItems(): Collection
    {
        return Item::amazingForHome();
    }

    /** @return Collection<int, Section> */
    public function homeSections(): Collection
    {
        return Section::forHome();
    }
}
