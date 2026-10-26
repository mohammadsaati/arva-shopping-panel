<?php

namespace App\Enums;

enum BannerTypeEnum: string
{
    use LabelTrait, ValuesTrait;
    case PRODUCT = 'PRODUCT';
    case CATEGORY = 'CATEGORY';

    case LINK = 'LINK';
}
