<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user || !$user->organization) {
            return redirect()->route('login');
        }

        $organization = $user->organization;

        // Check if organization has active subscription or trial
        if (!$organization->hasActiveSubscription()) {
            // Redirect to subscription page if not on trial and no active subscription
            return redirect()->route('subscription.index')
                ->with('error', 'Votre période d\'essai est terminée. Veuillez souscrire à un abonnement pour continuer.');
        }

        return $next($request);
    }
}
