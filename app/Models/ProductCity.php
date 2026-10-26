<?php

namespace App\Models;

use App\DTO\Product\ProductCityRateData;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property int $city_id
 * @property int $shipping_price
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Product $product
 * @property-read City $city
 */
#[Guarded('id')]
class ProductCity extends Model
{
    use HasFactory;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    protected function casts(): array
    {
        return ['shipping_price' => 'integer'];
    }

    /** @param array<int, ProductCityRateData> $rates */
    public static function replaceForProduct(Product $product, array $rates): void
    {
        $cityIds = array_map(static fn (ProductCityRateData $rate): int => $rate->cityId, $rates);

        self::query()
            ->where('product_id', $product->id)
            ->when($cityIds !== [], fn ($query) => $query->whereNotIn('city_id', $cityIds))
            ->delete();

        foreach ($rates as $rate) {
            self::query()->updateOrCreate(
                ['product_id' => $product->id, 'city_id' => $rate->cityId],
                ['shipping_price' => $rate->shippingPrice],
            );
        }
    }

    /**
     * @param array<int, int> $productIds
     * @param array<int, int> $cityIds
     * @return array<int, array<int, int>> City ID => product ID => shipping price.
     */
    public static function pricesForProductsAndCities(array $productIds, array $cityIds): array
    {
        if ($productIds === [] || $cityIds === []) {
            return [];
        }

        $prices = [];

        foreach (self::query()->whereIn('product_id', $productIds)->whereIn('city_id', $cityIds)->get() as $rate) {
            $prices[$rate->city_id][$rate->product_id] = $rate->shipping_price;
        }

        return $prices;
    }
}
