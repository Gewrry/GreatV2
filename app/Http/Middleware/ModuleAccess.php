<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * Checks if the authenticated user has access to the given module slug.
     * Super admins bypass all checks.
     *
     * Usage in routes: ->middleware('module:bpls')
     */
    public function handle(Request $request, Closure $next, string $moduleSlug): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!$user->hasModuleAccess($moduleSlug)) {
            abort(403, 'You do not have access to the ' . strtoupper($moduleSlug) . ' module.');
        }

        return $next($request);
    }
}
