<?php

namespace App\Models;

use App\DTO\Blog\BlogBannerCreateData;
use App\DTO\Blog\BlogBannerFilterData;
use App\DTO\Blog\BlogBannerUpdateData;
use App\Enums\GlobalStatusEnum;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string|null $eyebrow
 * @property string $title
 * @property string|null $description
 * @property string $image
 * @property string|null $mobile_image
 * @property string|null $target_url
 * @property string|null $target_label
 * @property int $sort_order
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class BlogBanner extends Model
{
    protected function casts(): array
    {
        return [
            'status' => GlobalStatusEnum::class,
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function startsAtInputValue(): ?string
    {
        return $this->starts_at?->format('Y-m-d\\TH:i');
    }

    public function endsAtInputValue(): ?string
    {
        return $this->ends_at?->format('Y-m-d\\TH:i');
    }

    public static function filter(BlogBannerFilterData $filters): Paginator
    {
        $allowedSorts = ['id', 'title', 'sort_order', 'status', 'starts_at', 'created_at'];
        $sorts = array_intersect_key($filters->sorts, array_flip($allowedSorts));
        $sorts = $sorts !== [] ? $sorts : ['id' => 'desc'];

        $query = self::query()
            ->when($filters->search !== null && $filters->search !== '', function (Builder $builder) use ($filters): void {
                $search = $filters->search;
                $builder->where(function (Builder $builder) use ($search): void {
                    $builder->where('title', 'like', "%{$search}%")
                        ->orWhere('eyebrow', 'like', "%{$search}%");
                });
            })
            ->when($filters->status !== null, fn (Builder $builder) => $builder->where('status', $filters->status->value));

        foreach ($sorts as $column => $direction) {
            $query->orderBy($column, $direction);
        }

        return $query->simplePaginate(perPage: $filters->perPage, page: $filters->page)->withQueryString();
    }

    public static function details(int $bannerId): self
    {
        return self::query()->findOrFail($bannerId);
    }

    /** @return Collection<int, self> */
    public static function forBlogHome(): Collection
    {
        return self::query()
            ->where('status', GlobalStatusEnum::ACTIVE->value)
            ->where(fn (Builder $query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn (Builder $query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(4)
            ->get();
    }

    public static function create(BlogBannerCreateData $data, string $imagePath, ?string $mobileImagePath): self
    {
        $banner = new self();
        $banner->eyebrow = $data->eyebrow;
        $banner->title = $data->title;
        $banner->description = $data->description;
        $banner->image = $imagePath;
        $banner->mobile_image = $mobileImagePath;
        $banner->target_url = $data->targetUrl;
        $banner->target_label = $data->targetLabel;
        $banner->sort_order = $data->sortOrder;
        $banner->starts_at = $data->startsAt;
        $banner->ends_at = $data->endsAt;
        $banner->status = $data->status;
        $banner->save();

        return $banner;
    }

    public function updateDetails(BlogBannerUpdateData $data, ?string $imagePath, ?string $mobileImagePath): self
    {
        foreach ($data->toModelAttributes() as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        if ($imagePath !== null) {
            $this->image = $imagePath;
        }

        if ($mobileImagePath !== null) {
            $this->mobile_image = $mobileImagePath;
        } elseif ($data->removeMobileImage) {
            $this->mobile_image = null;
        }

        $this->save();

        return $this;
    }

    public function deleteRecord(): void
    {
        $this->delete();
    }
}
