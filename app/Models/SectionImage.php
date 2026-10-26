<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $section_id
 * @property string $image
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class SectionImage extends Model
{
    use HasFactory;

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public static function findForSection(int $sectionId, int $imageId): self
    {
        return self::query()
            ->where('section_id', $sectionId)
            ->findOrFail($imageId);
    }

    public static function createForSection(Section $section, string $imagePath): self
    {
        $image = new self();
        $image->section_id = $section->id;
        $image->image = $imagePath;
        $image->save();

        return $image;
    }

    public function removeImage(): string
    {
        $path = $this->image;
        $this->delete();

        return $path;
    }
}
