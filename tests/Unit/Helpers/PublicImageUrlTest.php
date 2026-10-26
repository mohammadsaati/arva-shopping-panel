<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

class PublicImageUrlTest extends TestCase
{
    public function test_null_path_uses_default_asset_fallback(): void
    {
        $this->assertSame('/assets/media/images/1.jpg', public_image_url(null));
    }

    public function test_blank_path_uses_default_asset_fallback(): void
    {
        $this->assertSame('/assets/media/images/1.jpg', public_image_url('   '));
    }

    public function test_asset_path_stays_public_and_root_relative(): void
    {
        $this->assertSame(
            '/assets/media/blog/editorial-selection.png',
            public_image_url('assets/media/blog/editorial-selection.png')
        );
    }

    public function test_storage_path_is_root_relative(): void
    {
        $this->assertSame(
            '/storage/blog/posts/cover.jpg',
            public_image_url('blog/posts/cover.jpg')
        );
    }

    public function test_storage_prefixed_path_is_not_doubled(): void
    {
        $this->assertSame(
            '/storage/blog/posts/cover.jpg',
            public_image_url('storage/blog/posts/cover.jpg')
        );
    }

    public function test_absolute_url_is_kept(): void
    {
        $this->assertSame(
            'https://cdn.example.com/cover.jpg',
            public_image_url('https://cdn.example.com/cover.jpg')
        );
    }
}
