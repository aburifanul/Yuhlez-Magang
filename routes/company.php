<?php

use Illuminate\Support\Facades\Route;

Route::prefix('company')
    ->name('company.')
    ->middleware(['auth', 'role:company'])
    ->group(function () {

        // Route khusus company
    });