<?php

namespace App\DTO;

use Illuminate\Http\UploadedFile;

final readonly class SiteSettingUpdateData
{
    public function __construct(
        public string $siteName,
        public ?string $siteTagline,
        public ?string $supportPhone,
        public ?string $supportPhoneSecondary,
        public ?string $supportHours,
        public ?string $contactEmail,
        public ?string $contactAddress,
        public ?string $footerDescription,
        public ?string $copyrightText,
        public ?string $socialTitle,
        public ?string $newsletterTitle,
        public ?string $newsletterDescription,
        public ?string $appDownloadTitle,
        public ?string $appDownloadDescription,
        public ?string $metaTitle,
        public ?string $metaDescription,
        public bool $robotsIndex,
        public bool $robotsFollow,
        public ?string $googleSiteVerification,
        public ?string $bingSiteVerification,
        public ?UploadedFile $logo,
        public ?UploadedFile $footerLogo,
        public ?UploadedFile $favicon,
        public ?UploadedFile $appLogo,
        public ?UploadedFile $ogImage,
        public bool $removeLogo,
        public bool $removeFooterLogo,
        public bool $removeFavicon,
        public bool $removeAppLogo,
        public bool $removeOgImage,
        public array $providedFields,
    ) {
    }

    /** @return array<string, string|bool|null> */
    public function attributes(): array
    {
        $attributes = [
            'site_name' => $this->siteName,
            'site_tagline' => $this->siteTagline,
            'support_phone' => $this->supportPhone,
            'support_phone_secondary' => $this->supportPhoneSecondary,
            'support_hours' => $this->supportHours,
            'contact_email' => $this->contactEmail,
            'contact_address' => $this->contactAddress,
            'footer_description' => $this->footerDescription,
            'copyright_text' => $this->copyrightText,
            'social_title' => $this->socialTitle,
            'newsletter_title' => $this->newsletterTitle,
            'newsletter_description' => $this->newsletterDescription,
            'app_download_title' => $this->appDownloadTitle,
            'app_download_description' => $this->appDownloadDescription,
            'meta_title' => $this->metaTitle,
            'meta_description' => $this->metaDescription,
            'robots_index' => $this->robotsIndex,
            'robots_follow' => $this->robotsFollow,
            'google_site_verification' => $this->googleSiteVerification,
            'bing_site_verification' => $this->bingSiteVerification,
        ];

        return array_intersect_key($attributes, array_flip($this->providedFields));
    }

    /** @return array<string, array{0: ?UploadedFile, 1: bool}> */
    public function images(): array
    {
        return [
            'logo_path' => [$this->logo, $this->removeLogo],
            'footer_logo_path' => [$this->footerLogo, $this->removeFooterLogo],
            'favicon_path' => [$this->favicon, $this->removeFavicon],
            'app_logo_path' => [$this->appLogo, $this->removeAppLogo],
            'og_image_path' => [$this->ogImage, $this->removeOgImage],
        ];
    }
}
