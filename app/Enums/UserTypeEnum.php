<?php

namespace App\Enums;

enum UserTypeEnum: string
{
    use LabelTrait, ValuesTrait;

    case NORMAL = 'NORMAL';
    case VENDOR = 'VENDOR';
}
