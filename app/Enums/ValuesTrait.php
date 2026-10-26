<?php
declare(strict_types=1);

namespace App\Enums;

use App\DTO\KeyValueType;

trait ValuesTrait
{
    /**
     * @return list<int|string>
     */
    public static function values(): array
    {
        $values = [];
        foreach (self::cases() as $item) {
            $values[] = $item->value;
        }

        return $values;
    }

    public static function valuesAsString(): string
    {
        $values = [];
        foreach (self::cases() as $item) {
            $values[] = $item->value;
        }

        return implode(',', $values);
    }

    /**
     * @return array<KeyValueType>|KeyValueType[]
     */
    public static function toKeyValueType(): array
    {
        $tmp = [];
        foreach (self::cases() as $item) {
            $tmp[] = key_value_type_object(
                title: $item->trans(),
                value: $item->value
            );
        }

        return $tmp;
    }
}
