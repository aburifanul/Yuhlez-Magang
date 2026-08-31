<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user ke Google OAuth.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google.
     */
    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if (!$user) {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'role' => UserRole::INTERN,
                'google_access_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'google_token_expires_at' => $googleUser->expiresIn
                    ? now()->addSeconds($googleUser->expiresIn)
                    : null,
                'google_token_scope' => $googleUser->approvedScopes
                    ? implode(' ', $googleUser->approvedScopes)
                    : null,
                'email_verified_at' => now(),
            ]);
        } else {
            $user->update([
                'google_id' => $user->google_id ?: $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'google_access_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken ?: $user->google_refresh_token,
                'google_token_expires_at' => $googleUser->expiresIn
                    ? now()->addSeconds($googleUser->expiresIn)
                    : $user->google_token_expires_at,
                'google_token_scope' => $googleUser->approvedScopes
                    ? implode(' ', $googleUser->approvedScopes)
                    : $user->google_token_scope,
            ]);
        }

        Auth::login($user);

        return match ($user->role) {
            UserRole::ROOT => redirect()->route('root.dashboard'),
            UserRole::COMPANY => redirect()->route('company.dashboard'),
            UserRole::INTERN => redirect()->route('intern.dashboard'),
        };
    }
}