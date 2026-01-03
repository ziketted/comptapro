<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use App\Models\AccessLog;
use Illuminate\Http\Request;

class LogUserLogin
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        try {
            $user = $event->user->fresh();
            
            // Update user's last login info
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $this->request->ip()
            ]);

            // Only create access log if user has an organization
            if (!$user || !$user->organization_id) {
                return;
            }

            // Create access log entry
            AccessLog::create([
                'organization_id' => $user->organization_id,
                'user_id' => $user->id,
                'action' => 'login',
                'ip_address' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't break login if logging fails
            \Log::debug('Failed to log user login: ' . $e->getMessage());
        }
    }
}