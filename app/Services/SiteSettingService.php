<?php

namespace App\Services;

use App\DTO\SiteSettingUpdateData;
use App\Models\FooterAppLink;
use App\Models\FooterFeature;
use App\Models\FooterLinkGroup;
use App\Models\FooterSocialLink;
use App\Models\SiteSetting;
use App\Services\Contracts\SiteSettingServiceInterface;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SiteSettingService implements SiteSettingServiceInterface
{
    private bool $loaded = false;

    private ?SiteSetting $settings = null;

    public function current(): ?SiteSetting
    {
        if (! $this->loaded) {
            $this->settings = SiteSetting::current();
            $this->loaded = true;
        }

        return $this->settings;
    }

    public function siteName(): string
    {
        return $this->current()?->site_name ?: config('app.name', 'فروشگاه');
    }

    public function update(SiteSettingUpdateData $data): SiteSetting
    {
        $previous = $this->current();
        $paths = [];
        $newPaths = [];
        $oldPaths = [];

        try {
            foreach ($data->images() as $column => [$file, $remove]) {
                if ($file !== null) {
                    $path = $file->store('site', 'public');
                    $paths[$column] = $path;
                    $newPaths[] = $path;
                } elseif ($remove) {
                    $paths[$column] = null;
                }

                if (array_key_exists($column, $paths) && $previous?->getAttribute($column)) {
                    $oldPaths[] = $previous->getAttribute($column);
                }
            }

            $settings = SiteSetting::saveSettings($data, $paths);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($newPaths);
            throw $exception;
        }

        Storage::disk('public')->delete($oldPaths);
        $this->settings = $settings;
        $this->loaded = true;

        return $settings;
    }

    public function footer(): array
    {
        return [
            'features' => FooterFeature::visible(),
            'groups' => FooterLinkGroup::visibleWithLinks(),
            'socialLinks' => FooterSocialLink::visible(),
            'appLinks' => FooterAppLink::visible(),
        ];
    }
}
