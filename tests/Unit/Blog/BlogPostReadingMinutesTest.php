<?php

namespace Tests\Unit\Blog;

use App\Models\BlogPost;
use Tests\TestCase;

class BlogPostReadingMinutesTest extends TestCase
{
    public function test_model_delegates_to_reading_time_helper(): void
    {
        $post = new BlogPost([
            'body' => implode(' ', array_fill(0, 181, 'کلمه')),
            'excerpt' => null,
        ]);

        $this->assertSame(2, $post->readingMinutes());
    }
}
