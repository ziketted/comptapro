<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\User;
use App\Models\LicenseKey;

class SuperAdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'tenants_total' => Tenant::count(),
            'tenants_active' => Tenant::where('status', 'ACTIVE')->count(),
            'users_total' => User::count(),
            'licenses_active' => LicenseKey::where('status', 'USED')->where('expires_at', '>', now())->count(),
        ];

        return view('superadmin.dashboard', compact('stats'));
    }

    public function tenants()
    {
        $tenants = Tenant::withCount('users')->latest()->paginate(20);
        return view('superadmin.tenants', compact('tenants'));
    }

    public function users()
    {
        $users = User::with('tenant')->latest()->paginate(20);
        return view('superadmin.users', compact('users'));
    }

    public function licenses()
    {
        return view('superadmin.licenses');
    }

    public function toggleTenantStatus(Tenant $tenant)
    {
        if ($tenant->status === 'SUSPENDED') {
            // Restore to ACTIVE or TRIAL depending on dates
            $newStatus = ($tenant->on_trial && $tenant->trial_ends_at && $tenant->trial_ends_at->isFuture()) ? 'TRIAL' : 'ACTIVE';
            $tenant->update(['status' => $newStatus]);
            $message = "L'organisation {$tenant->name} a été réactivée.";
        } else {
            $tenant->update(['status' => 'SUSPENDED']);
            $message = "L'organisation {$tenant->name} a été suspendue.";
        }

        return back()->with('success', $message);
    }
}
