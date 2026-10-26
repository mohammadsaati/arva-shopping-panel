<?php

namespace App\Livewire\Storefront;

use App\DTO\Storefront\CatalogFilterData;
use App\Services\Contracts\StorefrontServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Catalog extends Component
{
    use WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(as: 'category', except: '')]
    public string $categoryId = '';

    #[Url(as: 'brand', except: '')]
    public string $brandId = '';

    #[Url(as: 'min_price', except: '')]
    public string $minPrice = '';

    #[Url(as: 'max_price', except: '')]
    public string $maxPrice = '';

    #[Url(as: 'available', except: true)]
    public bool $inStockOnly = true;

    #[Url(except: 'latest')]
    public string $sort = 'latest';

    public int $perPage = 12;

    public string $mobilePanel = '';

    #[Locked]
    public ?int $fixedCategoryId = null;

    protected StorefrontServiceInterface $storefrontService;

    public function boot(StorefrontServiceInterface $storefrontService): void
    {
        $this->storefrontService = $storefrontService;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatedBrandId(): void
    {
        $this->resetPage();
    }

    public function updatedMinPrice(): void
    {
        $this->resetPage();
    }

    public function updatedMaxPrice(): void
    {
        $this->resetPage();
    }

    public function updatedInStockOnly(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, [12, 24, 36], true)) {
            $this->perPage = 12;
        }

        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->categoryId = '';
        $this->brandId = '';
        $this->minPrice = '';
        $this->maxPrice = '';
        $this->inStockOnly = true;
        $this->resetPage();
    }

    public function removeFilter(string $filter): void
    {
        if ($filter === 'inStockOnly') {
            $this->inStockOnly = true;
        } elseif (in_array($filter, ['search', 'categoryId', 'brandId', 'minPrice', 'maxPrice'], true)) {
            $this->{$filter} = '';
        } else {
            return;
        }

        $this->resetPage();
    }

    public function setSort(string $sort): void
    {
        $this->sort = in_array($sort, ['latest', 'price_asc', 'price_desc'], true) ? $sort : 'latest';
        $this->mobilePanel = '';
        $this->resetPage();
    }

    public function openMobilePanel(string $panel): void
    {
        if (in_array($panel, ['filters', 'sort'], true)) {
            $this->mobilePanel = $panel;
        }
    }

    public function closeMobilePanel(): void
    {
        $this->mobilePanel = '';
    }

    public function render(): View
    {
        $sorts = match ($this->sort) {
            'price_asc' => ['price' => 'asc'],
            'price_desc' => ['price' => 'desc'],
            default => ['created_at' => 'desc'],
        };

        $categoryId = $this->fixedCategoryId ?? $this->nonNegativeInt($this->categoryId);
        $brandId = $this->nonNegativeInt($this->brandId);
        $categories = $this->fixedCategoryId === null ? $this->storefrontService->navigationCategories() : [];
        $brands = $this->storefrontService->catalogBrands();
        $activeFilters = [];

        if (trim($this->search) !== '') {
            $activeFilters['search'] = 'جستجو: '.$this->search;
        }

        if ($this->fixedCategoryId === null && $this->categoryId !== '') {
            foreach ($categories as $category) {
                if ((string) $category->id === $this->categoryId) {
                    $activeFilters['categoryId'] = 'دسته‌بندی: '.$category->name;
                    break;
                }

                foreach ($category->children as $childCategory) {
                    if ((string) $childCategory->id === $this->categoryId) {
                        $activeFilters['categoryId'] = 'دسته‌بندی: '.$childCategory->name;
                        break 2;
                    }
                }
            }
        }

        if ($this->brandId !== '') {
            $activeFilters['brandId'] = 'برند: '.($brands[$this->brandId] ?? $this->brandId);
        }

        if ($this->minPrice !== '') {
            $activeFilters['minPrice'] = 'از '.number_format((int) $this->minPrice).' تومان';
        }

        if ($this->maxPrice !== '') {
            $activeFilters['maxPrice'] = 'تا '.number_format((int) $this->maxPrice).' تومان';
        }

        if (! $this->inStockOnly) {
            $activeFilters['inStockOnly'] = 'شامل کالاهای ناموجود';
        }

        return view('livewire.storefront.catalog', [
            'items' => $this->storefrontService->catalogItems(new CatalogFilterData(
                search: trim($this->search) !== '' ? trim($this->search) : null,
                categoryId: $categoryId,
                brandId: $brandId,
                minPrice: $this->nonNegativeInt($this->minPrice),
                maxPrice: $this->nonNegativeInt($this->maxPrice),
                inStockOnly: $this->inStockOnly,
                perPage: $this->perPage,
                page: $this->getPage(),
                sorts: $sorts,
            )),
            'categories' => $categories,
            'brands' => $brands,
            'activeFilters' => $activeFilters,
            'activeFilterCount' => count($activeFilters),
        ]);
    }

    private function nonNegativeInt(string $value): ?int
    {
        return $value !== '' && ctype_digit($value) ? (int) $value : null;
    }
}
