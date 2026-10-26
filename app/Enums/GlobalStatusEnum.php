<?php

namespace App\Enums;

enum GlobalStatusEnum: string
{
    use LabelTrait, ValuesTrait;

    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::INACTIVE => 'danger',
        };
    }
}
