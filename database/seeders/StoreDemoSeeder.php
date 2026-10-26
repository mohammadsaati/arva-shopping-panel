<?php

namespace Database\Seeders;

use App\Enums\BannerTypeEnum;
use App\Enums\SliderTypeEnum;
use App\Models\Banner;
use App\Models\Brand;
use App\Models\Category;
use App\Models\City;
use App\Models\Color;
use App\Models\Item;
use App\Models\Product;
use App\Models\ProductCity;
use App\Models\ProductImage;
use App\Models\Section;
use App\Models\SectionImage;
use App\Models\Size;
use App\Models\Slider;
use App\Models\User;
use App\Models\UserProfile;
use Database\Factories\SizeFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class StoreDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->copyPublicImageAssetsToStorage();

        $brands = Brand::query()->orderBy('id')->limit(30)->get();
        if ($brands->count() < 30) {
            $brands = $brands->concat(Brand::factory()->count(30 - $brands->count())->create());
        }

        $categories = Category::query()->orderBy('id')->limit(30)->get();
        if ($categories->count() < 30) {
            $categories = $categories->concat(Category::factory()->count(30 - $categories->count())->create());
        }

        $colors = Color::query()->orderBy('id')->limit(30)->get();
        if ($colors->count() < 30) {
            $colors = $colors->concat(Color::factory()->count(30 - $colors->count())->create());
        }

        $sizes = Size::query()->get();
        $existingSizeValues = $sizes->pluck('value')->all();

        foreach (array_diff(SizeFactory::VALUES, $existingSizeValues) as $sizeValue) {
            $sizes->push(Size::factory()->create(['value' => $sizeValue]));
        }
        $users = User::query()->orderBy('id')->limit(120)->get();
        if ($users->count() < 120) {
            $users = $users->concat(User::factory()->count(120 - $users->count())->create());
        }

        $profiledUserIds = UserProfile::query()
            ->whereIn('user_id', $users->take(100)->pluck('id'))
            ->pluck('user_id')
            ->all();

        $users->take(100)
            ->reject(fn (User $user): bool => in_array($user->id, $profiledUserIds))
            ->each(
            fn (User $user) => UserProfile::factory()->for($user)->create(),
        );

        $items = collect();

        foreach ($categories as $category) {
            $categoryItems = Item::query()
                ->where('category_id', $category->id)
                ->orderBy('id')
                ->get();

            $missingItemsCount = max(0, 200 - $categoryItems->count());
            if ($missingItemsCount > 0) {
                $newItems = Item::factory()
                    ->count($missingItemsCount)
                    ->state(fn (): array => [
                        'category_id' => $category->id,
                        'brand_id' => fake()->boolean(75) ? $brands->random()->id : null,
                    ])
                    ->create();

                $categoryItems = $categoryItems->concat($newItems);
            }

            $items = $items->concat($categoryItems);
        }

        $products = Product::query()->orderBy('id')->limit(200)->get();
        $missingProductsCount = max(0, 200 - $products->count());
        if ($missingProductsCount > 0) {
            $newProducts = Product::factory()
                ->count($missingProductsCount)
                ->state(fn (): array => [
                    'item_id' => $items->random()->id,
                    'color_id' => fake()->boolean(80) ? $colors->random()->id : null,
                    'size_id' => fake()->boolean(80) ? $sizes->random()->id : null,
                ])
                ->create();

            $products = $products->concat($newProducts);
        }

        foreach ($products->take(30) as $product) {
            if (! $product->images()->exists()) {
                ProductImage::factory()->count(fake()->numberBetween(2, 4))->for($product)->create();
            }
        }

        $shippingCities = City::query()->orderBy('id')->limit(20)->get();

        if ($shippingCities->isNotEmpty()) {
            foreach ($products->take(20) as $product) {
                if ($product->cityRates()->exists()) {
                    continue;
                }

                foreach ($shippingCities->random(min(2, $shippingCities->count())) as $city) {
                    ProductCity::factory()->for($product)->for($city)->create();
                }
            }
        }

        $sections = Section::query()->orderBy('id')->limit(8)->get();
        if ($sections->count() < 8) {
            $sections = $sections->concat(Section::factory()->count(8 - $sections->count())->create());
        }

        foreach ($sections as $section) {
            if (! $section->images()->exists()) {
                SectionImage::factory()->count(fake()->numberBetween(1, 4))->for($section)->create();
            }

            if (! $section->items()->exists()) {
                $itemIds = $items->random(min(25, $items->count()))->pluck('id')->all();
                $section->items()->attach($itemIds);
            }
        }

        $missingBannersCount = max(0, 12 - Banner::query()->count());
        Banner::factory()
            ->count($missingBannersCount)
            ->state(function () use ($products, $categories): array {
                $type = fake()->randomElement(BannerTypeEnum::cases());

                return [
                    'type' => $type,
                    'product_id' => $type === BannerTypeEnum::PRODUCT ? $products->random()->id : null,
                    'category_id' => $type === BannerTypeEnum::CATEGORY ? $categories->random()->id : null,
                    'link' => $type === BannerTypeEnum::LINK ? fake()->url() : null,
                ];
            })
            ->create();

        $missingSlidersCount = max(0, 12 - Slider::query()->count());
        Slider::factory()
            ->count($missingSlidersCount)
            ->state(function () use ($products, $categories, $sections): array {
                $type = fake()->randomElement(SliderTypeEnum::cases());

                return [
                    'type' => $type,
                    'product_id' => $type === SliderTypeEnum::PRODUCT ? $products->random()->id : null,
                    'category_id' => $type === SliderTypeEnum::CATEGORY ? $categories->random()->id : null,
                    'section_id' => $type === SliderTypeEnum::SESSION ? $sections->random()->id : null,
                    'link' => $type === SliderTypeEnum::LINK ? fake()->url() : null,
                ];
            })
            ->create();
    }

    private function copyPublicImageAssetsToStorage(): void
    {
        $sourceFiles = glob(public_path('assets/media/images/*')) ?: [];

        foreach ($sourceFiles as $sourceFile) {
            if (! is_file($sourceFile) || preg_match('/\.(jpe?g|png|webp|gif)$/i', $sourceFile) !== 1) {
                continue;
            }

            $contents = file_get_contents($sourceFile);

            if ($contents === false) {
                continue;
            }

            Storage::disk('public')->put(
                'assets/media/images/' . basename($sourceFile),
                $contents,
            );
        }
    }
}
