<?php

namespace Tests\Unit\Support;

use App\Support\BlogReadingTime;
use PHPUnit\Framework\TestCase;

class BlogReadingTimeTest extends TestCase
{
    public function test_empty_text_is_one_minute(): void
    {
        $this->assertSame(1, BlogReadingTime::minutes(null, null));
        $this->assertSame(1, BlogReadingTime::minutes('   ', ''));
    }

    public function test_html_tags_are_ignored(): void
    {
        $this->assertSame(1, BlogReadingTime::minutes('<p>یک دو سه</p>'));
    }

    public function test_excerpt_is_included_in_word_count(): void
    {
        $body = implode(' ', array_fill(0, 100, 'کلمه'));
        $excerpt = implode(' ', array_fill(0, 80, 'متن'));

        $this->assertSame(1, BlogReadingTime::minutes($body, $excerpt));
    }

    public function test_rounds_up_to_the_next_minute(): void
    {
        $body = implode(' ', array_fill(0, 181, 'کلمه'));

        $this->assertSame(2, BlogReadingTime::minutes($body));
    }
}
