<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. SuperAdmin bypass
        if ($user && $user->role === 'superadmin') {
            return $next($request);
        }

        // 2. Exclude subscription routes to avoid infinite loop
        if ($request->is('subscription*') || $request->is('logout')) {
            return $next($request);
        }

        // 3. Check Tenant Subscription
        if ($user && $user->tenant) {
            // Update status first to ensure we have the latest state (handle expiration)
            $user->tenant->updateStatus(); 
            
            if (!$user->tenant->hasActiveSubscription()) {
                // Determine if it's a trial expiration or license expiration for better UX?
                // For now, redirect to generic expired page.
                return redirect()->route('subscription.expired');
            }
        }

        return $next($request);
    }
}
