<?php

namespace Tests\Unit\Admin;

use App\Livewire\Admin\Discounts\DiscountTargetPicker;
use Livewire\Livewire;
use Tests\TestCase;

class DiscountTargetPickerFilterTest extends TestCase
{
    public function test_active_filter_count_sums_criteria(): void
    {
        $picker = new DiscountTargetPicker;
        $picker->search = '0912';
        $picker->selectedOnly = true;

        $this->assertSame(2, $picker->activeFilterCount());
    }

    public function test_reset_filters_clears_list_criteria(): void
    {
        Livewire::test(DiscountTargetPicker::class)
            ->set('search', '0912')
            ->set('selectedOnly', true)
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('selectedOnly', false);
    }
}
