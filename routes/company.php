<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Company\CompanyController;
use App\Http\Controllers\Company\InternshipProgramController;
use App\Http\Controllers\Company\InternshipProgramBannerController;
use App\Http\Controllers\Company\InternshipPositionController;
use App\Http\Controllers\Company\InternshipRegistrationController;
use App\Http\Controllers\Company\InternshipParticipantController;

Route::prefix('company')
    ->name('company.')
    ->middleware(['auth', 'role:company'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::view('/', 'company.dashboard')
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | PROFILE
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/profile',
            [CompanyController::class, 'index']
        )->name('profile');

        Route::get(
            '/profile/edit',
            [CompanyController::class, 'edit']
        )->name('profile.edit');

        Route::put(
            '/profile',
            [CompanyController::class, 'update']
        )->name('profile.update');


        /*
        |--------------------------------------------------------------------------
        | INTERNSHIP PROGRAMS
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'internship-programs',
            InternshipProgramController::class
        );


        /*
        |--------------------------------------------------------------------------
        | INTERNSHIP PROGRAM BANNERS
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'internship-programs.banners',
            InternshipProgramBannerController::class
        );


        /*
        |--------------------------------------------------------------------------
        | INTERNSHIP POSITIONS
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'internship-programs.positions',
            InternshipPositionController::class
        );


        /*
        |--------------------------------------------------------------------------
        | INTERNSHIP REGISTRATIONS
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/internship-programs/{internshipProgram}/registrations',
            [InternshipRegistrationController::class, 'index']
        )->name('internship-programs.registrations.index');

        Route::get(
            '/internship-programs/{internshipProgram}/registrations/{registration}',
            [InternshipRegistrationController::class, 'show']
        )->name('internship-programs.registrations.show');

        Route::put(
            '/internship-programs/{internshipProgram}/registrations/{registration}/accept',
            [InternshipRegistrationController::class, 'accept']
        )->name('internship-programs.registrations.accept');

        Route::put(
            '/internship-programs/{internshipProgram}/registrations/{registration}/reject',
            [InternshipRegistrationController::class, 'reject']
        )->name('internship-programs.registrations.reject');

        /*
        |--------------------------------------------------------------------------
        | INTERNSHIP PARTICIPANTS
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'internship-programs.participants',
            InternshipParticipantController::class
        );

    });