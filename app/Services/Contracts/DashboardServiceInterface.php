<?php

namespace App\Services\Contracts;

use App\DTO\Dashboard\DashboardOverviewData;

interface DashboardServiceInterface
{
    public function overview(): DashboardOverviewData;
}
