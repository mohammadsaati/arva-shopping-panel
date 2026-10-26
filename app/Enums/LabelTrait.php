<?php
declare(strict_types=1);

namespace App\Enums;

trait LabelTrait
{
    public function trans(): string
    {
        if (empty($this->labelKey())) {
            return trans('enums.' . $this->value);
        }

        return trans('enums.' . $this->labelKey() . '.' . $this->value);
    }

    public function label(): string
    {
        if (empty($this->labelKey())) {
            return trans('enums.' . $this->value);
        }

        return trans('enums.' . $this->labelKey() . '.' . $this->value);
    }

    protected function labelKey(): string
    {
        return '';
    }
}
