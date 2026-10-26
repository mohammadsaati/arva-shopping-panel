<?php

namespace App\Enums;

enum SpecificationTypeEnum: string
{
    use LabelTrait, ValuesTrait;

    case TEXT = 'text';
    case NUMBER = 'number';
    case BOOLEAN = 'boolean';
    case SELECT = 'select';
    case MULTI_SELECT = 'multi_select';

    protected function labelKey(): string
    {
        return 'specification_type';
    }

    public function usesOptions(): bool
    {
        return in_array($this, [self::SELECT, self::MULTI_SELECT], true);
    }
}
