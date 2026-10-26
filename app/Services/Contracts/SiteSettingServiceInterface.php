<?php

namespace App\Services\Contracts;

use App\DTO\SiteSettingUpdateData;
use App\Models\SiteSetting;

interface SiteSettingServiceInterface
{
    public function current(): ?SiteSetting;

    public function siteName(): string;

    public function update(SiteSettingUpdateData $data): SiteSetting;

    /** @return array<string, mixed> */
    public function footer(): array;
}
