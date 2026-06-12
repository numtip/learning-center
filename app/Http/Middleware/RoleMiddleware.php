<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();
        if (!$user) {
            abort(403);
        }

        // รองรับทั้ง , และ |
        $roles = collect($roles)
            ->flatMap(fn ($r) => explode('|', $r))
            ->toArray();

        $userRoles = $user->roles->pluck('name')->toArray();

        if (!array_intersect($roles, $userRoles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
