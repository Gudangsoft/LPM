<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$permissions
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Admin has all permissions
        if ($request->user()->isAdmin()) {
            return $next($request);
        }

        // Check if user has any of the specified permissions
        if ($request->user()->hasAnyPermission($permissions)) {
            return $next($request);
        }

        abort(403, 'Anda tidak memiliki izin untuk melakukan aksi ini.');
    }
}
