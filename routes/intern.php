<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:intern'])
    ->prefix('intern')
    ->name('intern.')
    ->group(function () {

        Route::get('/', function () {
            return response()->json([
                'success' => true,
                'message' => 'Intern access OK',
                'user' => auth()->user()->name,
                'role' => auth()->user()->role->value,
            ]);
        })->name('dashboard');

    });