<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use UnitEnum;
use BackedEnum;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Use the user resolved by the auth middleware (jwt.auth / auth:*).
        $user = $request->user();
        if (! $user) {
            return error_response('unauthenticated', 401);
        }

        // Normalize the user's role to a string for reliable comparison
        $userRole = $this->normalizeRole($user->role);

        // Normalize allowed roles from the route middleware param
        $allowed = array_map(fn($r) => $this->normalizeRole($r), $roles);

        if (! in_array($userRole, $allowed, true)) {
            return error_response('forbidden', 403);
        }

        return $next($request);
    }

    private function normalizeRole($role): string
    {
        // If your model casts 'role' to an Enum (e.g., UserRoleEnum)
        if ($role instanceof BackedEnum) {
            return strtolower((string) $role->value); // string-backed enum
        }
        if ($role instanceof UnitEnum) {
            return strtolower((string) $role->name);  // pure enum (name)
        }
        // If it's already a string/int from DB
        return strtolower((string) $role);
    }
}
