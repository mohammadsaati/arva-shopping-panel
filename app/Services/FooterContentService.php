<?php

namespace App\Services;

use App\DTO\FooterEntryData;
use App\DTO\FooterEntryFilterData;
use App\Models\FooterAppLink;
use App\Models\FooterFeature;
use App\Models\FooterLink;
use App\Models\FooterLinkGroup;
use App\Models\FooterSocialLink;
use App\Services\Contracts\FooterContentServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class FooterContentService implements FooterContentServiceInterface
{
    public function kinds(): array
    {
        return [
            'features' => 'مزیت‌های خرید',
            'groups' => 'ستون‌های لینک',
            'links' => 'لینک‌های فوتر',
            'social' => 'شبکه‌های اجتماعی',
            'apps' => 'لینک‌های اپلیکیشن',
        ];
    }

    public function entry(string $kind, int $id): Model
    {
        $modelClass = $this->model($kind);

        return $modelClass::details($id);
    }

    public function groupOptions(): array
    {
        return FooterLinkGroup::adminAll()->pluck('title', 'id')->all();
    }

    public function filter(string $kind, FooterEntryFilterData $filters): Paginator
    {
        $modelClass = $this->model($kind);

        return $modelClass::filter($filters);
    }

    public function save(FooterEntryData $data, ?int $id = null): void
    {
        $modelClass = $this->model($data->kind);
        $entry = $id === null ? null : $modelClass::details($id);
        $hasImage = in_array($data->kind, ['features', 'social', 'apps'], true);
        $oldImage = $hasImage ? $entry?->getAttribute('image_path') : null;
        $newImage = null;
        $attributes = $data->attributes();

        try {
            if ($data->image !== null) {
                $newImage = $data->image->store('footer/'.$data->kind, 'public');
                $attributes['image_path'] = $newImage;
            } elseif ($data->removeImage && $entry !== null) {
                $attributes['image_path'] = null;
            }

            if ($entry === null) {
                $modelClass::createEntry($attributes);
            } else {
                $entry->updateEntry($attributes);
            }
        } catch (Throwable $exception) {
            if ($newImage !== null) Storage::disk('public')->delete($newImage);
            throw $exception;
        }

        if ($oldImage !== null && ($newImage !== null || $data->removeImage)) {
            Storage::disk('public')->delete($oldImage);
        }
    }

    public function delete(string $kind, int $id): void
    {
        $entry = $this->entry($kind, $id);

        if ($kind === 'groups' && $entry->hasLinks()) {
            throw ValidationException::withMessages(['group' => 'ابتدا لینک‌های این ستون را حذف یا به ستون دیگری منتقل کنید.']);
        }

        $image = in_array($kind, ['features', 'social', 'apps'], true) ? $entry->getAttribute('image_path') : null;
        $entry->deleteEntry();
        if ($image !== null) Storage::disk('public')->delete($image);
    }

    /** @return class-string<Model> */
    private function model(string $kind): string
    {
        return match ($kind) {
            'features' => FooterFeature::class,
            'groups' => FooterLinkGroup::class,
            'links' => FooterLink::class,
            'social' => FooterSocialLink::class,
            'apps' => FooterAppLink::class,
            default => abort(404),
        };
    }
}
