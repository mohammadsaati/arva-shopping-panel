@php($settings = site_settings())

<footer class="user-footer" id="about">
    <div class="user-footer__container">
        <div class="user-footer__top">
            <div class="user-footer__identity">
                @if ($settings?->footer_logo_path ?? $settings?->logo_path)
                    <img src="{{ public_image_url($settings->footer_logo_path ?? $settings->logo_path) }}" alt="{{ site_name() }}" class="user-footer__logo" />
                @else
                    <strong class="user-footer__wordmark">{{ site_name() }}</strong>
                @endif
                @if ($settings?->site_tagline)<span class="user-footer__tagline">{{ $settings->site_tagline }}</span>@endif
            </div>
            <button type="button" class="user-footer__back-top" onclick="window.scrollTo({top:0,behavior:'smooth'})">
                بازگشت به بالا
                <i class="ki-filled ki-arrow-up" aria-hidden="true"></i>
            </button>
        </div>

        @if ($settings?->support_phone || $settings?->support_phone_secondary || $settings?->support_hours || $settings?->contact_email)
            <ul class="user-footer__support" aria-label="راه‌های تماس">
                @if ($settings?->support_phone)
                    <li>
                        <span class="user-footer__contact-seal" aria-hidden="true"><i class="ki-filled ki-phone"></i></span>
                        <span class="user-footer__contact-copy">
                            <small>تلفن پشتیبانی</small>
                            <a href="{{ phone_href($settings->support_phone) }}" dir="ltr">{{ $settings->support_phone }}</a>
                        </span>
                    </li>
                @endif
                @if ($settings?->support_phone_secondary)
                    <li>
                        <span class="user-footer__contact-seal" aria-hidden="true"><i class="ki-filled ki-call"></i></span>
                        <span class="user-footer__contact-copy">
                            <small>تلفن دیگر</small>
                            <a href="{{ phone_href($settings->support_phone_secondary) }}" dir="ltr">{{ $settings->support_phone_secondary }}</a>
                        </span>
                    </li>
                @endif
                @if ($settings?->support_hours)
                    <li>
                        <span class="user-footer__contact-seal" aria-hidden="true"><i class="ki-filled ki-time"></i></span>
                        <span class="user-footer__contact-copy">
                            <small>ساعات پاسخ‌گویی</small>
                            <em>{{ $settings->support_hours }}</em>
                        </span>
                    </li>
                @endif
                @if ($settings?->contact_email)
                    <li>
                        <span class="user-footer__contact-seal" aria-hidden="true"><i class="ki-filled ki-sms"></i></span>
                        <span class="user-footer__contact-copy">
                            <small>نامه الکترونیکی</small>
                            <a href="mailto:{{ $settings->contact_email }}" dir="ltr">{{ $settings->contact_email }}</a>
                        </span>
                    </li>
                @endif
            </ul>
        @endif

        @if ($features->isNotEmpty())
            <ul class="user-footer__features" aria-label="مزیت‌های خرید">
                @foreach ($features as $feature)
                    <li>
                        @if ($feature->url)<a href="{{ $feature->url }}" class="user-footer__feature">@else<div class="user-footer__feature">@endif
                            @if ($feature->image_path)
                                <img src="{{ public_image_url($feature->image_path) }}" alt="" loading="lazy" />
                            @elseif ($feature->icon)
                                <i class="{{ $feature->icon }}" aria-hidden="true"></i>
                            @else
                                <i class="ki-filled ki-verify" aria-hidden="true"></i>
                            @endif
                            <span>{{ $feature->title }}</span>
                        @if ($feature->url)</a>@else</div>@endif
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="user-footer__body">
            <div class="user-footer__columns">
                @forelse ($groups as $group)
                    <div class="user-footer__column">
                        <h2>{{ $group->title }}</h2>
                        <nav aria-label="{{ $group->title }}">
                            @foreach ($group->links as $link)
                                <a href="{{ $link->url }}" @if($link->opens_in_new_tab) target="_blank" rel="noopener noreferrer" @endif>{{ $link->label }}</a>
                            @endforeach
                        </nav>
                    </div>
                @empty
                    <div class="user-footer__column">
                        <h2>دسترسی سریع</h2>
                        <nav aria-label="دسترسی سریع">
                            <a href="{{ route('home') }}">صفحه اصلی</a>
                            <a href="{{ route('shop.index') }}">فروشگاه</a>
                            <a href="{{ route('blog.index') }}">مجله {{ site_name() }}</a>
                            <a href="{{ route('cart.index') }}">سبد خرید</a>
                        </nav>
                    </div>
                @endforelse
            </div>

            <div class="user-footer__connect">
                @if ($socialLinks->isNotEmpty())
                    <div class="user-footer__social">
                        <h2>{{ $settings?->social_title ?: 'همراه ما باشید' }}</h2>
                        <div class="user-footer__social-links">
                            @foreach ($socialLinks as $social)
                                <a href="{{ $social->url }}" aria-label="{{ $social->label }}" title="{{ $social->label }}" target="_blank" rel="noopener noreferrer">
                                    @if ($social->image_path)<img src="{{ public_image_url($social->image_path) }}" alt="" loading="lazy" />
                                    @elseif ($social->icon)<i class="{{ $social->icon }}" aria-hidden="true"></i>
                                    @else<span>{{ mb_substr($social->label, 0, 1) }}</span>@endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="user-footer__newsletter">
                    <h2>{{ $settings?->newsletter_title ?: 'از تازه‌ترین خبرها و تخفیف‌ها باخبر شوید' }}</h2>
                    @if ($settings?->newsletter_description)<p>{{ $settings->newsletter_description }}</p>@endif
                    <x-card.form :action="route('newsletter.store')" class="user-footer__newsletter-form">
                        <x-form.input name="email" type="email" label="ایمیل شما" placeholder="example@email.com" dir="ltr" required />
                        <button type="submit">ثبت ایمیل</button>
                    </x-card.form>
                    @error('email', 'newsletter')<p class="user-footer__error" role="alert">{{ $message }}</p>@enderror
                    @if (session('newsletter_success'))<p class="user-footer__notice" role="status">{{ session('newsletter_success') }}</p>@endif
                </div>
            </div>
        </div>

        @if ($appLinks->isNotEmpty())
            <div class="user-footer__app">
                <div class="user-footer__app-heading">
                    @if ($settings?->app_logo_path)<img src="{{ public_image_url($settings->app_logo_path) }}" alt="" />@endif
                    <div><strong>{{ $settings?->app_download_title ?: 'اپلیکیشن '.site_name() }}</strong>@if ($settings?->app_download_description)<span>{{ $settings->app_download_description }}</span>@endif</div>
                </div>
                <div class="user-footer__app-links">
                    @foreach ($appLinks as $appLink)
                        <a href="{{ $appLink->url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $appLink->label }}">
                            @if ($appLink->image_path)<img src="{{ public_image_url($appLink->image_path) }}" alt="{{ $appLink->label }}" loading="lazy" />@else{{ $appLink->label }}@endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="user-footer__legal">
            @if ($settings?->footer_description)<p class="user-footer__description">{{ $settings->footer_description }}</p>@endif
            @if ($settings?->contact_address)<p class="user-footer__address">{{ $settings->contact_address }}</p>@endif
            <div class="user-footer__copyright">{{ $settings?->copyright_text ?: '© '.jalali_date(now(), 'Y').' '.site_name().' — تمام حقوق محفوظ است.' }}</div>
        </div>
    </div>
</footer>
