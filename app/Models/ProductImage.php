<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property string $image
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Product $product
 */
#[Guarded('id')]
class ProductImage extends Model
{
    use HasFactory;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public static function createForProduct(Product $product, string $path): self
    {
        $image = new self();
        $image->product_id = $product->id;
        $image->image = $path;
        $image->save();

        return $image;
    }

    public static function findForProduct(int $productId, int $imageId): self
    {
        return self::query()
            ->where('product_id', $productId)
            ->whereKey($imageId)
            ->firstOrFail();
    }

    public function replaceImage(string $path): string
    {
        $oldPath = $this->image;
        $this->image = $path;
        $this->save();

        return $oldPath;
    }

    public function removeImage(): string
    {
        $path = $this->image;
        $this->delete();

        return $path;
    }
}
