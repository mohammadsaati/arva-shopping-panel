<?php

namespace App\Models;

use App\DTO\Section\SectionCreateData;
use App\DTO\Section\SectionFilterData;
use App\DTO\Section\SectionUpdateData;
use App\Enums\GlobalStatusEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $bg_color
 * @property string|null $bg_image
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class Section extends Model
{
    use HasFactory;

    public function images(): HasMany
    {
        return $this->hasMany(SectionImage::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'item_sections')->withTimestamps();
    }

    public function sliders(): HasMany
    {
        return $this->hasMany(Slider::class);
    }

    protected function casts(): array
    {
        return ['status' => GlobalStatusEnum::class];
    }

    public static function filter(SectionFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'name', 'slug', 'status', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->withCount(['images', 'items', 'sliders'])
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('status', $filters->status->value));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->simplePaginate(perPage: $filters->perPage, page: $filters->page)->withQueryString();
    }

    public static function details(int $sectionId): self
    {
        return self::query()
            ->with(['images', 'items:id,name,code'])
            ->withCount(['images', 'items', 'sliders'])
            ->findOrFail($sectionId);
    }

    public static function publicDetailsBySlug(string $slug): self
    {
        return self::query()
            ->where('slug', $slug)
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->firstOrFail();
    }

    /**
     * @param array<int, string> $imagePaths
     * @param array<int, int> $itemIds
     */
    public static function create(SectionCreateData $data, ?string $bgImagePath, array $imagePaths): self
    {
        return DB::transaction(function () use ($data, $bgImagePath, $imagePaths): self {
            $section = new self();
            $section->name = $data->name;
            $section->slug = $data->slug;
            $section->bg_color = $data->bgColor;
            $section->bg_image = $bgImagePath;
            $section->status = $data->status;
            $section->save();

            foreach ($imagePaths as $imagePath) {
                SectionImage::createForSection($section, $imagePath);
            }

            $section->items()->sync($data->itemIds);

            return $section;
        });
    }

    /**
     * @param array<int, string> $imagePaths
     * @return array<int, string> Paths of gallery images removed by this update.
     */
    public function updateDetails(SectionUpdateData $data, ?string $bgImagePath, array $imagePaths): array
    {
        return DB::transaction(function () use ($data, $bgImagePath, $imagePaths): array {
            foreach ($data->toModelAttributes() as $attribute => $value) {
                $this->setAttribute($attribute, $value);
            }

            if ($bgImagePath !== null) {
                $this->bg_image = $bgImagePath;
            } elseif ($data->removeBgImage) {
                $this->bg_image = null;
            }

            $this->save();

            $removedImagePaths = [];

            foreach ($data->deleteImageIds as $imageId) {
                $removedImagePaths[] = SectionImage::findForSection($this->id, $imageId)->removeImage();
            }

            foreach ($imagePaths as $imagePath) {
                SectionImage::createForSection($this, $imagePath);
            }

            if ($data->itemIdsProvided) {
                $this->items()->sync($data->itemIds ?? []);
            }

            return $removedImagePaths;
        });
    }

    /** @return array<int, string> */
    public static function activeOptions(?int $includeSectionId = null): array
    {
        return self::query()
            ->where(function (Builder $builder) use ($includeSectionId): void {
                $builder->where('status', GlobalStatusEnum::ACTIVE->value)
                    ->when($includeSectionId !== null, fn (Builder $builder) => $builder->orWhereKey($includeSectionId));
            })
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /** @return Collection<int, self> */
    public static function forHome(int $limit = 6, int $itemsPerSection = 8): Collection
    {
        return self::query()
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->whereHas('items', fn (Builder $items) => $items->availableForHome())
            ->with([
                'images',
                'items' => fn ($items) => $items
                    ->availableForHome()
                    ->with([
                        'category',
                        'brand',
                        'products' => fn ($products) => $products
                            ->where('status', GlobalStatusEnum::ACTIVE->value)
                            ->where('stock', '>', 0)
                            ->orderBy('price')
                            ->limit(1),
                    ])
                    ->orderBy('items.id')
                    ->limit(max(1, min($itemsPerSection, 12))),
            ])
            ->orderBy('id')
            ->limit(max(1, min($limit, 10)))
            ->get();
    }
}
