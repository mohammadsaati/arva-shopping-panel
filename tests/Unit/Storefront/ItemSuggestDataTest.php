<?php

namespace Tests\Unit\Storefront;

use App\DTO\Storefront\ItemSuggestData;
use PHPUnit\Framework\TestCase;

class ItemSuggestDataTest extends TestCase
{
    public function test_normalized_term_trims_spaces(): void
    {
        $data = new ItemSuggestData(term: '  کرم  ');

        $this->assertSame('کرم', $data->normalizedTerm());
    }

    public function test_should_query_rejects_short_terms(): void
    {
        $this->assertFalse((new ItemSuggestData(term: 'ک'))->shouldQuery());
        $this->assertFalse((new ItemSuggestData(term: ' '))->shouldQuery());
    }

    public function test_should_query_accepts_two_or_more_characters(): void
    {
        $this->assertTrue((new ItemSuggestData(term: 'کر'))->shouldQuery());
        $this->assertTrue((new ItemSuggestData(term: 'کرم'))->shouldQuery());
    }

    public function test_capped_limit_stays_between_one_and_twelve(): void
    {
        $this->assertSame(1, (new ItemSuggestData(term: 'کرم', limit: 0))->cappedLimit());
        $this->assertSame(8, (new ItemSuggestData(term: 'کرم'))->cappedLimit());
        $this->assertSame(12, (new ItemSuggestData(term: 'کرم', limit: 40))->cappedLimit());
    }
}
