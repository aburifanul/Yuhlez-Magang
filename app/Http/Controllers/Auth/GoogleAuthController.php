<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }


    /**
     * Login menggunakan email dan password.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);


        /*
        |--------------------------------------------------------------------------
        | Jika user sudah login
        |--------------------------------------------------------------------------
        */

        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }


        /*
        |--------------------------------------------------------------------------
        | Login
        |--------------------------------------------------------------------------
        */

        if (!Auth::attempt(
            [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ],
            $request->boolean('remember')
        )) {

            return back()
                ->withErrors([
                    'email' => 'Email atau password yang dimasukkan salah.',
                ])
                ->withInput(
                    $request->only('email', 'remember')
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil user
        |--------------------------------------------------------------------------
        */

        $user = Auth::user();


        /*
        |--------------------------------------------------------------------------
        | Cek status akun
        |--------------------------------------------------------------------------
        |
        | Sesuaikan bagian ini jika sistem status user kamu berbeda.
        |
        */

        if (
            isset($user->status) &&
            $user->status !== null &&
            $user->status == 0
        ) {

            Auth::logout();

            return back()
                ->withErrors([
                    'email' => 'Akun kamu sedang tidak aktif. Silakan hubungi administrator.',
                ])
                ->withInput(
                    $request->only('email')
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Regenerate session
        |--------------------------------------------------------------------------
        */

        $request->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Redirect berdasarkan role
        |--------------------------------------------------------------------------
        */

        return $this->redirectByRole($user);
    }


    /**
     * Redirect ke Google OAuth.
     */
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }


    /**
     * Callback setelah login menggunakan Google.
     */
    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->user();


        /*
        |--------------------------------------------------------------------------
        | Cari user berdasarkan Google ID atau Email
        |--------------------------------------------------------------------------
        */

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();


        /*
        |--------------------------------------------------------------------------
        | User belum ada
        |--------------------------------------------------------------------------
        */

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

            /*
            |--------------------------------------------------------------------------
            | User sudah ada
            |--------------------------------------------------------------------------
            */

            $user->update([

                'google_id' => $user->google_id
                    ?: $googleUser->getId(),

                'avatar' => $googleUser->getAvatar(),

                'google_access_token' => $googleUser->token,

                'google_refresh_token' => $googleUser->refreshToken
                    ?: $user->google_refresh_token,

                'google_token_expires_at' => $googleUser->expiresIn
                    ? now()->addSeconds($googleUser->expiresIn)
                    : $user->google_token_expires_at,

                'google_token_scope' => $googleUser->approvedScopes
                    ? implode(' ', $googleUser->approvedScopes)
                    : $user->google_token_scope,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Cek status akun
        |--------------------------------------------------------------------------
        */

        if (
            isset($user->status) &&
            $user->status !== null &&
            $user->status == 0
        ) {

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Akun kamu sedang tidak aktif. Silakan hubungi administrator.',
                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Login user
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        request()->session()->regenerate();


        /*
        |--------------------------------------------------------------------------
        | Redirect berdasarkan role
        |--------------------------------------------------------------------------
        */

        return $this->redirectByRole($user);
    }


    /**
     * Redirect berdasarkan role user.
     */
    private function redirectByRole(User $user): RedirectResponse
    {
        return match ($user->role) {

            UserRole::ROOT =>
                redirect()->route('root.dashboard'),

            UserRole::COMPANY =>
                redirect()->route('company.dashboard'),

            UserRole::INTERN =>
                redirect()->route('intern.dashboard'),

            default =>
                redirect()->route('login')
                    ->withErrors([
                        'email' => 'Role akun tidak dikenali.',
                    ]),
        };
    }
}
