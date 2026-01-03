<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->tenant_id) {
            $tenant = Auth::user()->tenant;

            if ($tenant) {
                // Update status if needed (e.g. trial ended)
                $tenant->updateStatus();

                // If tenant is expired, block business features
                if ($tenant->isExpired()) {
                    // List of routes that should ALWAYS be accessible
                    $allowedRoutes = [
                        'profile.edit',
                        'profile.update',
                        'profile.destroy',
                        'logout',
                        'dashboard', // Allow dashboard to show "Expired" notice
                    ];

                    if (!in_array($request->route()->getName(), $allowedRoutes)) {
                        if ($request->expectsJson()) {
                            return response()->json(['error' => 'Your trial has expired. Please subscribe to continue.'], 403);
                        }

                        return redirect()->route('dashboard')->with('error', 'Votre période d\'essai ou votre licence a expiré. Veuillez activer une nouvelle licence pour continuer à utiliser toutes les fonctionnalités.');
                    }
                }
            }
        }

        return $next($request);
    }
}
