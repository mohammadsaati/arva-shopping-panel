<?php

namespace App\Models;

use App\DTO\SiteSettingUpdateData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $singleton_key
 * @property string $site_name
 * @property string|null $site_tagline
 * @property string|null $logo_path
 * @property string|null $footer_logo_path
 * @property string|null $favicon_path
 * @property string|null $support_phone
 * @property string|null $support_phone_secondary
 * @property string|null $support_hours
 * @property string|null $contact_email
 * @property string|null $contact_address
 * @property string|null $footer_description
 * @property string|null $copyright_text
 * @property string|null $social_title
 * @property string|null $newsletter_title
 * @property string|null $newsletter_description
 * @property string|null $app_download_title
 * @property string|null $app_download_description
 * @property string|null $app_logo_path
 * @property string|null $meta_title
 * @property string|null $meta_description
 * @property string|null $og_image_path
 * @property bool $robots_index
 * @property bool $robots_follow
 * @property string|null $google_site_verification
 * @property string|null $bing_site_verification
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class SiteSetting extends Model
{
    protected function casts(): array
    {
        return [
            'robots_index' => 'boolean',
            'robots_follow' => 'boolean',
        ];
    }

    public static function current(): ?self
    {
        return self::query()->where('singleton_key', 1)->first();
    }

    /** @param array<string, string|null> $imagePaths */
    public static function saveSettings(SiteSettingUpdateData $data, array $imagePaths): self
    {
        $settings = self::current() ?? new self(['singleton_key' => 1]);

        foreach ($data->attributes() as $column => $value) {
            $settings->setAttribute($column, $value);
        }

        foreach (['logo_path', 'footer_logo_path', 'favicon_path', 'app_logo_path', 'og_image_path'] as $column) {
            if (array_key_exists($column, $imagePaths)) {
                $settings->setAttribute($column, $imagePaths[$column]);
            }
        }

        $settings->save();

        return $settings;
    }
}
