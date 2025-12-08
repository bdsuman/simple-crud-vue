<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HTTPLoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // save activity log for DELETE, UPDATE, and CREATE requests
        $user = auth()->user();
        $method = $request->method();

        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $event = match ($method) {
                'POST' => 'create',
                'PUT', 'PATCH' => 'update',
                'DELETE' => 'delete',
                default => 'unknown',
            };

            $activityLogger = activity()
                ->causedBy($user)
                ->withProperties([
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url' => $request->fullUrl(),
                    'method' => $method,
                    'request' => $request->all(),
                ])
                ->event($request->is('api/mobile/*') ? "user_{$event}" : "admin_{$event}")
                ->tap(function ($activity) {
                    $activity->log_name = request()->is('api/mobile/*') ? 'mobile_app_activity' : 'admin_app_activity';
                });

            if ($user instanceof \Illuminate\Database\Eloquent\Model) {
                $activityLogger->performedOn($user);
            }

            $activityLogger->log(
                "User [{$user?->id} -> {$user?->full_name}] is trying URL: {$request->fullUrl()}"
            );
        }

        return $next($request);
    }
}
