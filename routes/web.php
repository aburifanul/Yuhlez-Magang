<?php

use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Dashboard\HomeController;
use App\Http\Controllers\Dashboard\PerusahaanController;
use App\Http\Controllers\Intern\DashboardController;
use App\Http\Controllers\Dashboard\MagangController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;






Route::get('/yuhlez-magang', [HomeController::class, 'index'])
    ->name('dashboard.index');

// ruote magang
Route::get('/magang', [MagangController::class, 'index'])
    ->name('magang.index');

// route detail magang (harus login)
Route::get('/magang/{slug}', function ($slug) {

    return redirect()->route('login', [
        'redirect' => url()->current()
    ]);

})->name('magang.detail');


Route::get('/perusahaan', [PerusahaanController::class, 'index'])
    ->name('dashboard.perusahaan');

// route ke intern
Route::middleware(['auth'])->prefix('intern')->name('intern.')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

});

// route login


Route::get('/login', [GoogleAuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [GoogleAuthController::class, 'login'])
    ->name('login.process');

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('google.callback');

    
// Route::get('/login', function () {
//     return view('auth.login');
// })->name('login');

// Route::post('/login', [GoogleAuthController::class, 'login'])
//     ->name('login.store');


// // Register
// Route::get('/register', [GoogleAuthController::class, 'showRegister'])
//     ->name('register');

// Route::post('/register', [GoogleAuthController::class, 'register'])
//     ->name('register.store');

/*
|--------------------------------------------------------------------------
| Google Authentication
|--------------------------------------------------------------------------
*/

Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])
    ->name('google.redirect');

Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])
    ->name('google.callback');

// Logout
Route::post('/logout', [GoogleAuthController::class, 'logout'])
    ->name('logout');


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