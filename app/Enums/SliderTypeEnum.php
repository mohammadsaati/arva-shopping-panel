<?php

namespace App\Enums;

enum SliderTypeEnum: string
{
    use LabelTrait, ValuesTrait;
    case PRODUCT = 'PRODUCT';
    case CATEGORY = 'CATEGORY';

    case LINK = 'LINK';

    case SESSION = 'SESSION';
}
