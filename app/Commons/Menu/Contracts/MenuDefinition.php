<?php

namespace App\Commons\Menu\Contracts;

use App\Commons\Menu\MenuItem;

interface MenuDefinition
{
    /** @return MenuItem[] */
    public function items(): array;
}