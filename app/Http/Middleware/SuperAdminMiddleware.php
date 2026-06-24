<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Restrict the route to Super Admins (and the legacy Admin which is treated
     * as a full-access account for backward compatibility).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && in_array(Auth::user()->role, [UserRole::SUPER_ADMIN, UserRole::ADMIN], true)) {
            return $next($request);
        }

        abort(403, 'Unauthorized action.');
    }
}
