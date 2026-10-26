<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class ShoppingCartSession
{
    public static function key(Request $request): string
    {
        $shoppingKey = $request->session()->get('shopping_key');

        if (! is_string($shoppingKey) || $shoppingKey === '') {
            $shoppingKey = Str::random(64);
            $request->session()->put('shopping_key', $shoppingKey);
        }

        return $shoppingKey;
    }
}
