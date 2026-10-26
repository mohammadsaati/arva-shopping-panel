<?php

namespace App\DTO;

class KeyValueType
{
    public function __construct(
        public string $key,
        public mixed $value,
    )
    {
    }
}
