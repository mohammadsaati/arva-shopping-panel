<?php

namespace Tests\Unit\Admin;

use App\Livewire\Admin\Sections\SectionItemPicker;
use Livewire\Livewire;
use Tests\TestCase;

class SectionItemPickerFilterTest extends TestCase
{
    public function test_active_filter_count_sums_criteria(): void
    {
        $picker = new SectionItemPicker;
        $picker->search = 'phone';
        $picker->homeAvailability = 'available';
        $picker->selectedOnly = true;

        $this->assertSame(3, $picker->activeFilterCount());
    }

    public function test_reset_filters_clears_list_criteria(): void
    {
        Livewire::test(SectionItemPicker::class)
            ->set('search', 'phone')
            ->set('homeAvailability', 'available')
            ->set('selectedOnly', true)
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('homeAvailability', '')
            ->assertSet('selectedOnly', false);
    }
}
