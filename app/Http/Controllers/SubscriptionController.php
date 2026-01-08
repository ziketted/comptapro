<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LicenseKey;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Show the subscription status and activation page.
     */
    public function index()
    {
        $tenant = auth()->user()->tenant;
        $activeLicense = $tenant->activeLicense();
        return view('subscription.index', compact('tenant', 'activeLicense'));
    }

    /**
     * Show the expiration warning/blocking page.
     */
    public function expired()
    {
        $tenant = auth()->user()->tenant;
        
        // If actually active, redirect back to dashboard
        if ($tenant->hasActiveSubscription()) {
            return redirect()->route('dashboard');
        }

        return view('subscription.expired', compact('tenant'));
    }

    /**
     * Activate a license key.
     */
    public function activate(Request $request)
    {
        if (!auth()->user()->isManager()) {
            abort(403);
        }

        $request->validate([
            'license_key' => 'required|string|exists:license_keys,key',
        ]);

        $tenant = auth()->user()->tenant;
        $key = $request->input('license_key');

        // Check if key is unused
        $license = LicenseKey::where('key', $key)->where('status', 'UNUSED')->first();

        if (!$license) {
            return back()->with('error', 'Cette clé de licence est invalide ou déjà utilisée.');
        }

        // Activate
        DB::transaction(function () use ($tenant, $license) {
            $license->update([
                'tenant_id' => $tenant->id,
                'status' => 'USED',
                'activated_at' => now(),
                'expires_at' => now()->addDays(30),
            ]);

            $tenant->update([
                'status' => 'ACTIVE',
                'subscription_active' => true,
                'subscription_ends_at' => now()->addDays(30),
            ]);
            
            // If was on trial, maybe we should end the trial flag?
            // $tenant->update(['on_trial' => false]); // Optional logic
        });

        return redirect()->route('dashboard')->with('success', 'Abonnement activé avec succès pour 30 jours !');
    }
}
