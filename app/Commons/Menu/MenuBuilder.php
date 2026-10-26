<?php

namespace App\Commons\Menu;

/**
 * Fluent builder for MenuItem — construct items via chained calls, finalize with build().
 *
 * Example:
 *   MenuBuilder::make('Users')
 *       ->route('admin.users.index')
 *       ->icon('ki-profile-circle')
 *       ->roles(['admin'])
 *       ->routeGroup(['admin.users.*'])
 *       ->children([
 *           MenuBuilder::make('All Users')->route('admin.users.index')->build(),
 *       ])
 *       ->build();
 */
class MenuBuilder
{
    private ?string $routeName = null;
    private ?string $icon = null;
    private array $subMenus = [];
    private array $roles = [];
    private array $routeGroup = [];
    private ?MenuBadge $badge = null;

    private function __construct(private readonly string $title) {}

    public static function make(string $title): static
    {
        return new static($title);
    }

    public function route(string $routeName): static
    {
        $this->routeName = $routeName;
        return $this;
    }

    public function icon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }

    /** @param MenuItem[] $children */
    public function children(array $children): static
    {
        $this->subMenus = $children;
        return $this;
    }

    public function roles(array|string $roles): static
    {
        $this->roles = (array) $roles;
        return $this;
    }

    /**
     * Route name patterns that mark this item as "active".
     * Supports wildcards: 'admin.users.*' matches any route starting with 'admin.users.'.
     */
    public function routeGroup(array|string $routeGroup): static
    {
        $this->routeGroup = (array) $routeGroup;
        return $this;
    }

    /**
     * Attach a badge. Value may be static or a Closure for live counts.
     *
     * ->badge('New')
     * ->badge(fn() => Order::pending()->count(), 'danger')
     * ->badge(MenuBadge::make('Beta', 'warning'))
     */
    public function badge(string|int|\Closure|MenuBadge $value, string $color = 'primary'): static
    {
        $this->badge = $value instanceof MenuBadge ? $value : MenuBadge::make($value, $color);
        return $this;
    }

    public function build(): MenuItem
    {
        return new MenuItem(
            title: $this->title,
            routeName: $this->routeName,
            icon: $this->icon,
            subMenus: $this->subMenus,
            roles: $this->roles,
            routeGroup: $this->routeGroup,
            badge: $this->badge,
        );
    }
}
