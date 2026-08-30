<?php

use Illuminate\Support\Facades\Route;

Route::prefix('intern')
    ->name('intern.')
    ->middleware(['auth', 'role:intern'])
    ->group(function () {

        // Route khusus intern
    });