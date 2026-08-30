<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Root\CompanyController;

Route::prefix('root')
    ->name('root.')
    ->middleware(['auth', 'role:root'])
    ->group(function () {

        Route::resource(
            'companies',
            CompanyController::class
        );

    });