<?php

namespace App\Http\Controllers\Admin;

use App\DTO\SiteSettingUpdateData;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSiteSettingRequest;
use App\Services\Contracts\SiteSettingServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SiteSettingController extends Controller
{
    public function __construct(private readonly SiteSettingServiceInterface $settings)
    {
    }

    public function edit(): View
    {
        return view('admin.settings.edit', ['settings' => $this->settings->current()]);
    }

    public function update(UpdateSiteSettingRequest $request): RedirectResponse
    {
        $this->settings->update(new SiteSettingUpdateData(
            siteName: $request->string('site_name')->value(),
            siteTagline: $request->filled('site_tagline') ? $request->string('site_tagline')->value() : null,
            supportPhone: $request->filled('support_phone') ? $request->string('support_phone')->value() : null,
            supportPhoneSecondary: $request->filled('support_phone_secondary') ? $request->string('support_phone_secondary')->value() : null,
            supportHours: $request->filled('support_hours') ? $request->string('support_hours')->value() : null,
            contactEmail: $request->filled('contact_email') ? $request->string('contact_email')->value() : null,
            contactAddress: $request->filled('contact_address') ? $request->string('contact_address')->value() : null,
            footerDescription: $request->filled('footer_description') ? $request->string('footer_description')->value() : null,
            copyrightText: $request->filled('copyright_text') ? $request->string('copyright_text')->value() : null,
            socialTitle: $request->filled('social_title') ? $request->string('social_title')->value() : null,
            newsletterTitle: $request->filled('newsletter_title') ? $request->string('newsletter_title')->value() : null,
            newsletterDescription: $request->filled('newsletter_description') ? $request->string('newsletter_description')->value() : null,
            appDownloadTitle: $request->filled('app_download_title') ? $request->string('app_download_title')->value() : null,
            appDownloadDescription: $request->filled('app_download_description') ? $request->string('app_download_description')->value() : null,
            metaTitle: $request->filled('meta_title') ? $request->string('meta_title')->value() : null,
            metaDescription: $request->filled('meta_description') ? $request->string('meta_description')->value() : null,
            robotsIndex: $request->boolean('robots_index'),
            robotsFollow: $request->boolean('robots_follow'),
            googleSiteVerification: $request->filled('google_site_verification') ? $request->string('google_site_verification')->value() : null,
            bingSiteVerification: $request->filled('bing_site_verification') ? $request->string('bing_site_verification')->value() : null,
            logo: $request->file('logo'),
            footerLogo: $request->file('footer_logo'),
            favicon: $request->file('favicon'),
            appLogo: $request->file('app_logo'),
            ogImage: $request->file('og_image'),
            removeLogo: $request->boolean('remove_logo'),
            removeFooterLogo: $request->boolean('remove_footer_logo'),
            removeFavicon: $request->boolean('remove_favicon'),
            removeAppLogo: $request->boolean('remove_app_logo'),
            removeOgImage: $request->boolean('remove_og_image'),
            providedFields: $request->keys(),
        ));

        return redirect()->route('admin.settings.edit')->with('success', 'تنظیمات سایت ذخیره شد.');
    }
}
