<?php

namespace Tests\Unit\Blog;

use App\DTO\Blog\BlogCategoryPostsFilterData;
use PHPUnit\Framework\TestCase;

class BlogCategoryPostsFilterDataTest extends TestCase
{
    public function test_search_defaults_to_null(): void
    {
        $filters = new BlogCategoryPostsFilterData(categoryId: 4);

        $this->assertSame(4, $filters->categoryId);
        $this->assertNull($filters->search);
    }

    public function test_search_keeps_provided_term(): void
    {
        $filters = new BlogCategoryPostsFilterData(categoryId: 4, search: 'کرم ضدآفتاب');

        $this->assertSame('کرم ضدآفتاب', $filters->search);
    }
}
