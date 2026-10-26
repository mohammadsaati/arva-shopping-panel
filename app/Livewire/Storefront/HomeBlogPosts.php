<?php

namespace App\Livewire\Storefront;

use App\Services\Contracts\BlogStorefrontServiceInterface;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class HomeBlogPosts extends Component
{
    protected BlogStorefrontServiceInterface $blog;

    public function boot(BlogStorefrontServiceInterface $blog): void
    {
        $this->blog = $blog;
    }

    public function render(): View
    {
        return view('livewire.storefront.home-blog-posts', [
            'posts' => $this->blog->homeLatestPosts(),
        ]);
    }
}
