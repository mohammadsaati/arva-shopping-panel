<?php

namespace App\Commons\Menu;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/**
 * Composite pattern node — represents a single menu item or a group with children.
 */
class MenuItem
{
    /** @param MenuItem[] $subMenus */
    public function __construct(
        private readonly string $title,
        private readonly ?string $routeName = null,
        private readonly ?string $icon = null,
        private readonly array $subMenus = [],
        private readonly array $roles = [],
        private readonly array $routeGroup = [],
        private readonly ?MenuBadge $badge = null,
    ) {}

    public function getTitle(): string { return $this->title; }
    public function getRouteName(): ?string { return $this->routeName; }
    public function getIcon(): ?string { return $this->icon; }
    /** @return MenuItem[] */
    public function getSubMenus(): array { return $this->subMenus; }
    public function getRoles(): array { return $this->roles; }
    public function getRouteGroup(): array { return $this->routeGroup; }
    public function getBadge(): ?MenuBadge { return $this->badge; }

    public function hasChildren(): bool
    {
        return !empty($this->subMenus);
    }

    public function getUrl(): string
    {
        if ($this->routeName === null) {
            return '#';
        }

        try {
            return route($this->routeName);
        } catch (\Exception) {
            return '#';
        }
    }

    public function isActive(): bool
    {
        $current = Route::currentRouteName();

        if ($current === null) {
            return false;
        }

        if ($this->routeName !== null && $current === $this->routeName) {
            return true;
        }

        foreach ($this->routeGroup as $pattern) {
            if (str_ends_with($pattern, '*')) {
                if (str_starts_with($current, rtrim($pattern, '*'))) {
                    return true;
                }
            } elseif ($current === $pattern) {
                return true;
            }
        }

        foreach ($this->subMenus as $child) {
            if ($child->isActive()) {
                return true;
            }
        }

        return false;
    }

    public function isVisible(): bool
    {
        if (empty($this->roles)) {
            return true;
        }

        $user = Auth::user();

        if ($user === null) {
            return false;
        }

        if (method_exists($user, 'hasAnyRole')) {
            return $user->hasAnyRole($this->roles);
        }

        return in_array($user->role ?? null, $this->roles, strict: true);
    }

    /** @return MenuItem[] */
    public function getVisibleSubMenus(): array
    {
        return array_values(array_filter($this->subMenus, fn(MenuItem $item) => $item->isVisible()));
    }
}
