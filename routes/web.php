<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

/*
|--------------------------------------------------------------------------
| Google Authentication
|--------------------------------------------------------------------------
*/

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('google.callback');


require __DIR__.'/root.php';
require __DIR__.'/company.php';
require __DIR__.'/intern.php';


/*
|--------------------------------------------------------------------------
| Debug Authentication
|--------------------------------------------------------------------------
*/

Route::get('/debug/auth', function () {

    // Belum login
    if (!auth()->check()) {
        return response()->json([
            'success' => true,
            'authenticated' => false,
            'message' => 'User belum login.',
        ]);
    }

    // Sudah login
    $user = auth()->user();

    return response()->json([
        'success' => true,
        'authenticated' => true,
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'google_id' => $user->google_id,
        'role' => $user->role->value,
        'has_intern' => $user->intern()->exists(),
        'has_company' => $user->company()->exists(),
    ]);
});


/*
|--------------------------------------------------------------------------
| Debug Logout
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->get('/debug/logout', function () {

    Auth::logout();

    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return response()->json([
        'success' => true,
        'message' => 'Logout berhasil',
        'authenticated' => auth()->check(),
    ]);
})->name('debug.logout');