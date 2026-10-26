<?php

namespace App\Commons\Menu;

/**
 * Badge attached to a menu item. Value can be a static string/int or a
 * Closure evaluated at render time (useful for live counts from the DB).
 *
 * Colors: primary | success | danger | warning | info | dark
 *
 * Examples:
 *   MenuBadge::make('New')
 *   MenuBadge::make(fn() => Order::pending()->count(), 'danger')
 */
class MenuBadge
{
    private function __construct(
        private readonly string|int|\Closure $value,
        private readonly string $color = 'primary',
    ) {}

    public static function make(string|int|\Closure $value, string $color = 'primary'): static
    {
        return new static($value, $color);
    }

    /** Returns null when the resolved value is falsy (hides the badge). */
    public function getValue(): string|int|null
    {
        $resolved = $this->value instanceof \Closure ? ($this->value)() : $this->value;
        return !empty($resolved) ? $resolved : null;
    }

    public function getColor(): string
    {
        return $this->color;
    }
}
