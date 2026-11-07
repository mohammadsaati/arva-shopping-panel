<?php

namespace App\Models;

use App\Enums\GlobalStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Guarded;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property GlobalStatusEnum $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
#[Guarded('id')]
class VendorType extends Model
{
    protected function casts(): array
    {
        return ['status' => GlobalStatusEnum::class];
    }

    public static function ensureGeneral(): self
    {
        return self::query()->firstOrCreate(
            ['name' => 'عمومی'],
            ['status' => GlobalStatusEnum::ACTIVE],
        );
    }
}
