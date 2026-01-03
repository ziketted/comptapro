<?php

namespace App\Listeners;

use App\Models\AccessLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;

class RecordLoginActivity
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        try {
            // Refresh user to get latest data
            $user = $event->user->fresh();
            
            // Only log if user has an organization
            if (!$user || !$user->organization_id) {
                return;
            }

            AccessLog::create([
                'organization_id' => $user->organization_id,
                'user_id' => $user->id,
                'action' => 'login',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't break login if logging fails
            \Log::debug('Failed to log access: ' . $e->getMessage());
        }
    }
}
