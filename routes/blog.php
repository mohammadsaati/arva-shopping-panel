<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;

Route::view('/blog', 'blog.index')->name('blog.index');

Route::controller(BlogController::class)->prefix('blog')->name('blog.')->group(function (): void {
    Route::get('/categories/{slug}', 'category')->name('categories.show');
    Route::get('/posts/{slug}', 'show')->name('posts.show');
});
