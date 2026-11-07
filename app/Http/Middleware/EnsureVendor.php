<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Contracts\VendorServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVendor
{
    public function __construct(private readonly VendorServiceInterface $vendors)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);
        $this->vendors->profileForUser($user);

        return $next($request);
    }
}
