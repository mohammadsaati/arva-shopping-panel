<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductImage;
use Database\Factories\Concerns\UsesPublicImageAssets;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProductImage> */
class ProductImageFactory extends Factory
{
    use UsesPublicImageAssets;

    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'image' => $this->randomPublicImagePath(),
        ];
    }
}
