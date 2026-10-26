<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSiteSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $shortText = ['nullable', 'string', 'max:255'];

        return [
            'site_name' => ['required', 'string', 'max:255'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'support_phone' => $shortText,
            'support_phone_secondary' => $shortText,
            'support_hours' => $shortText,
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_address' => ['nullable', 'string', 'max:2000'],
            'footer_description' => ['nullable', 'string', 'max:2000'],
            'copyright_text' => $shortText,
            'social_title' => $shortText,
            'newsletter_title' => $shortText,
            'newsletter_description' => ['nullable', 'string', 'max:2000'],
            'app_download_title' => $shortText,
            'app_download_description' => ['nullable', 'string', 'max:2000'],
            'meta_title' => $shortText,
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'robots_index' => ['nullable', 'boolean'],
            'robots_follow' => ['nullable', 'boolean'],
            'google_site_verification' => $shortText,
            'bing_site_verification' => $shortText,
            'logo' => ['nullable', 'image', 'max:5120'],
            'footer_logo' => ['nullable', 'image', 'max:5120'],
            'favicon' => ['nullable', 'image', 'max:2048'],
            'app_logo' => ['nullable', 'image', 'max:5120'],
            'og_image' => ['nullable', 'image', 'max:5120'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_footer_logo' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],
            'remove_app_logo' => ['nullable', 'boolean'],
            'remove_og_image' => ['nullable', 'boolean'],
        ];
    }
}
