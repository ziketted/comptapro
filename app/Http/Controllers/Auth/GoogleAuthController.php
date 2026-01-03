<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google OAuth
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google OAuth callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if user exists
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // User exists, just login
                Auth::login($user);
                
                // Check if user has organization
                if (!$user->organization_id) {
                    return redirect()->route('organization.setup');
                }
                
                return redirect()->route('dashboard');
            }
            
            // Create new user
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(uniqid()), // Random password for OAuth users
                'email_verified_at' => now(), // Auto-verify email for Google users
            ]);
            
            Auth::login($user);
            
            // Redirect to organization setup
            return redirect()->route('organization.setup');
            
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Erreur lors de la connexion avec Google: ' . $e->getMessage());
        }
    }
}
