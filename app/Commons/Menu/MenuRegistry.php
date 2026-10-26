<?php

namespace App\Commons\Menu;

use App\Commons\Menu\Contracts\MenuDefinition;

/**
 * Registry pattern — holds named menu definitions and resolves them on demand.
 * Register menus in AppServiceProvider::boot().
 */
class MenuRegistry
{
    /** @var array<string, MenuDefinition> */
    private static array $menus = [];

    public static function register(string $name, MenuDefinition $menu): void
    {
        static::$menus[$name] = $menu;
    }

    /** @return MenuItem[] */
    public static function all(string $name): array
    {
        return (static::$menus[$name] ?? null)?->items() ?? [];
    }

    /**
     * Returns only items the current user is allowed to see.
     *
     * @return MenuItem[]
     */
    public static function visible(string $name): array
    {
        return array_values(array_filter(static::all($name), fn(MenuItem $item) => $item->isVisible()));
    }

    public static function has(string $name): bool
    {
        return isset(static::$menus[$name]);
    }
}