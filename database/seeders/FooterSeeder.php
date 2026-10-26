<?php

namespace Database\Seeders;

use App\Enums\GlobalStatusEnum;
use App\Models\FooterAppLink;
use App\Models\FooterFeature;
use App\Models\FooterLink;
use App\Models\FooterLinkGroup;
use App\Models\FooterSocialLink;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FooterSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->seedFeatures();
            $this->seedLinkGroups();
            $this->seedSocialLinks();
            $this->seedAppLinks();
        });
    }

    private function seedFeatures(): void
    {
        $features = [
            ['title' => 'انتخاب آسان کالا', 'icon' => 'ki-filled ki-magnifier', 'url' => '/shop'],
            ['title' => 'اطلاعات کامل محصولات', 'icon' => 'ki-filled ki-information-2', 'url' => '/shop'],
            ['title' => 'راهنمای خرید در مجله', 'icon' => 'ki-filled ki-book', 'url' => '/blog'],
            ['title' => 'پیگیری سفارش‌ها', 'icon' => 'ki-filled ki-delivery', 'url' => '/account/orders'],
            ['title' => 'مدیریت حساب کاربری', 'icon' => 'ki-filled ki-profile-circle', 'url' => '/account'],
            ['title' => 'دسترسی سریع به سبد خرید', 'icon' => 'ki-filled ki-handcart', 'url' => '/cart'],
        ];

        foreach ($features as $order => $feature) {
            if (FooterFeature::query()->where('title', $feature['title'])->exists()) {
                continue;
            }

            FooterFeature::factory()->create([
                ...$feature,
                'sort_order' => $order,
                'status' => GlobalStatusEnum::ACTIVE,
            ]);
        }
    }

    private function seedLinkGroups(): void
    {
        $groups = [
            [
                'title' => 'راهنمای خرید',
                'links' => [
                    ['label' => 'همه محصولات', 'url' => '/shop'],
                    ['label' => 'سبد خرید', 'url' => '/cart'],
                    ['label' => 'پیگیری سفارش‌ها', 'url' => '/account/orders'],
                    ['label' => 'مجله و راهنمای خرید', 'url' => '/blog'],
                ],
            ],
            [
                'title' => 'حساب کاربری',
                'links' => [
                    ['label' => 'ورود به حساب', 'url' => '/login'],
                    ['label' => 'ساخت حساب کاربری', 'url' => '/register'],
                    ['label' => 'پروفایل من', 'url' => '/account'],
                    ['label' => 'سفارش‌های من', 'url' => '/account/orders'],
                ],
            ],
            [
                'title' => 'مطالب و راهنما',
                'links' => [
                    ['label' => 'مجله فروشگاه', 'url' => '/blog'],
                    ['label' => 'صفحه اصلی', 'url' => '/'],
                    ['label' => 'مشاهده محصولات', 'url' => '/shop'],
                    ['label' => 'سبد خرید', 'url' => '/cart'],
                ],
            ],
            [
                'title' => 'خرید و سفارش',
                'links' => [
                    ['label' => 'رفتن به فروشگاه', 'url' => '/shop'],
                    ['label' => 'مشاهده سبد خرید', 'url' => '/cart'],
                    ['label' => 'ثبت و پرداخت سفارش', 'url' => '/checkout'],
                    ['label' => 'پیگیری سفارش', 'url' => '/account/orders'],
                ],
            ],
        ];

        foreach ($groups as $groupOrder => $definition) {
            $group = FooterLinkGroup::query()->where('title', $definition['title'])->first();

            if (! $group) {
                $group = FooterLinkGroup::factory()->create([
                    'title' => $definition['title'],
                    'sort_order' => $groupOrder,
                    'status' => GlobalStatusEnum::ACTIVE,
                ]);
            }

            foreach ($definition['links'] as $linkOrder => $link) {
                if (FooterLink::query()
                    ->where('footer_link_group_id', $group->id)
                    ->where('label', $link['label'])
                    ->exists()) {
                    continue;
                }

                FooterLink::factory()->create([
                    'footer_link_group_id' => $group->id,
                    'label' => $link['label'],
                    'url' => $link['url'],
                    'sort_order' => $linkOrder,
                    'status' => GlobalStatusEnum::ACTIVE,
                ]);
            }
        }
    }

    private function seedSocialLinks(): void
    {
        $links = [
            ['label' => 'اینستاگرام فروشگاه', 'url' => 'https://instagram.com/your_store'],
            ['label' => 'کانال تلگرام فروشگاه', 'url' => 'https://t.me/your_store'],
            ['label' => 'صفحه فروشگاه در ایکس', 'url' => 'https://x.com/your_store'],
        ];

        foreach ($links as $order => $link) {
            if (FooterSocialLink::query()->where('label', $link['label'])->exists()) {
                continue;
            }

            FooterSocialLink::factory()->create([
                ...$link,
                'sort_order' => $order,
                'status' => GlobalStatusEnum::INACTIVE,
            ]);
        }
    }

    private function seedAppLinks(): void
    {
        $links = [
            ['label' => 'دانلود از کافه‌بازار', 'url' => 'https://cafebazaar.ir/app/your.package'],
            ['label' => 'دانلود از مایکت', 'url' => 'https://myket.ir/app/your.package'],
            ['label' => 'دانلود برای اندروید', 'url' => 'https://play.google.com/store/apps/details?id=your.package'],
            ['label' => 'دانلود برای iOS', 'url' => 'https://example.com/ios-app'],
        ];

        foreach ($links as $order => $link) {
            if (FooterAppLink::query()->where('label', $link['label'])->exists()) {
                continue;
            }

            FooterAppLink::factory()->create([
                ...$link,
                'sort_order' => $order,
                'status' => GlobalStatusEnum::INACTIVE,
            ]);
        }
    }
}
