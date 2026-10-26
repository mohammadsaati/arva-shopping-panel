<?php

namespace App\Livewire\Storefront;

use App\DTO\Blog\BlogCategoryPostsFilterData;
use App\Services\Contracts\BlogStorefrontServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BlogCategoryPosts extends Component
{
    use WithPagination;

    #[Locked]
    public string $slug = '';

    #[Url(except: 'latest')]
    public string $sort = 'latest';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    protected BlogStorefrontServiceInterface $blog;

    public function boot(BlogStorefrontServiceInterface $blog): void
    {
        $this->blog = $blog;
    }

    public function mount(string $slug): void
    {
        $this->slug = $slug;
    }

    public function setSort(string $sort): void
    {
        $this->sort = in_array($sort, ['latest', 'popular'], true) ? $sort : 'latest';
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->search = trim($this->search);
        $this->resetPage();
    }

    public function render(): View
    {
        $category = $this->blog->categoryBySlug($this->slug);
        abort_if($category === null, 404);

        $sorts = $this->sort === 'popular'
            ? ['views_count' => 'desc', 'published_at' => 'desc']
            : ['published_at' => 'desc'];

        return view('livewire.storefront.blog-category-posts', [
            'category' => $category,
            'categories' => $this->blog->homeCategories(),
            'posts' => $this->blog->categoryPosts(new BlogCategoryPostsFilterData(
                categoryId: $category->id,
                search: $this->search !== '' ? $this->search : null,
                page: $this->getPage(),
                sorts: $sorts,
            )),
        ]);
    }
}
