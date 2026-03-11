<?php
// app/Http/Middleware/ClientAuthenticated.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('client')->check()) {
            return redirect()->route('client.login')
                ->with('error', 'Please login to access the portal.');
        }

        $client = Auth::guard('client')->user();
        
        if (!$client->email_verified_at && !$request->routeIs('client.verify*') && !$request->routeIs('client.logout')) {
            return redirect()->route('client.verify.show')
                ->with('warning', 'Please verify your email address to continue.');
        }

        return $next($request);
    }
}