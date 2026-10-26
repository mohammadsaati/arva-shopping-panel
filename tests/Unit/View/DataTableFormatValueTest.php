<?php

namespace Tests\Unit\View;

use App\View\Components\DataTable;
use PHPUnit\Framework\TestCase;

class DataTableFormatValueTest extends TestCase
{
    public function test_format_value_returns_empty_placeholder_for_null(): void
    {
        $formatted = DataTable::formatValue(null, ['empty' => '—', 'format' => null]);

        $this->assertSame('—', $formatted);
    }

    public function test_format_value_formats_numbers(): void
    {
        $formatted = DataTable::formatValue(12000, ['empty' => '-', 'format' => 'number']);

        $this->assertSame('12,000', $formatted);
    }
}
