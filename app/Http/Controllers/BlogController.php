<?php

namespace App\Http\Controllers;

use App\Services\Contracts\BlogStorefrontServiceInterface;
use Illuminate\Contracts\View\View;

class BlogController extends Controller
{
    public function __construct(private readonly BlogStorefrontServiceInterface $blog) {}

    public function category(string $slug): View
    {
        $category = $this->blog->categoryBySlug($slug);
        abort_if($category === null, 404);

        return view('blog.category', [
            'categorySlug' => $slug,
            'pageTitle' => $category->name,
        ]);
    }

    public function show(string $slug): View
    {
        $post = $this->blog->postBySlug($slug);

        return view('blog.show', [
            'post' => $post,
            'categories' => $this->blog->homeCategories(),
            'relatedPosts' => $this->blog->relatedPosts($post),
            'previousPost' => $this->blog->previousPost($post),
            'nextPost' => $this->blog->nextPost($post),
        ]);
    }
}
