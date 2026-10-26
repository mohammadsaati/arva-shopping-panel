<?php

namespace App\Livewire\Storefront;

use App\Services\Contracts\BlogStorefrontServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class BlogHome extends Component
{
    #[Url(as: 'category', except: '')]
    public string $categorySlug = '';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    protected BlogStorefrontServiceInterface $blog;

    public function boot(BlogStorefrontServiceInterface $blog): void
    {
        $this->blog = $blog;
    }

    public function updatedSearch(): void
    {
        $this->search = trim($this->search);
    }

    public function render(): View
    {
        $selectedCategory = $this->categorySlug !== ''
            ? $this->blog->categoryBySlug($this->categorySlug)
            : null;
        $categoryId = $selectedCategory?->id;
        $isSearching = $this->search !== '';
        $featuredPosts = $isSearching ? collect() : $this->blog->featuredPosts($categoryId);
        $latestPosts = $isSearching
            ? $this->blog->searchPosts($this->search, $categoryId)
            : $this->blog->latestPosts($categoryId);
        $slides = $featuredPosts->isNotEmpty() ? $featuredPosts : ($isSearching ? collect() : $latestPosts->take(4));
        $articles = $isSearching
            ? $latestPosts
            : $slides->concat($latestPosts)->unique('id')->take(8)->values();


        return view('livewire.storefront.blog-home', [
            'selectedCategory' => $selectedCategory,
            'categories' => $this->blog->homeCategories(),
            'slides' => $slides,
            'articles' => $articles,
            'popularArticles' => $isSearching ? collect() : $articles->sortByDesc('views_count')->take(4)->values(),
            'banners' => $isSearching ? collect() : $this->blog->homeBanners(),
            'amazingItems' => $isSearching ? collect() : $this->blog->amazingItems(),
            'publishedCount' => $this->blog->publishedCount($categoryId),
            'isSearching' => $isSearching,
        ]);
    }
}
