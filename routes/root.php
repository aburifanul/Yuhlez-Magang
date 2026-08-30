<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Root\UserController;
use App\Http\Controllers\Root\CompanyController;
use App\Http\Controllers\Root\InternController;
use App\Http\Controllers\Root\InternshipProgramController;
use App\Http\Controllers\Root\InternshipProgramBannerController;
use App\Http\Controllers\Root\InternshipPositionController;
use App\Http\Controllers\Root\InternshipRegistrationController;
use App\Http\Controllers\Root\InternshipParticipantController;
use App\Http\Controllers\Root\CertificateController;
use App\Http\Controllers\Root\WorkController;
use App\Http\Controllers\Root\WorkPhotoController;
use App\Http\Controllers\Root\WorkMemberController;

Route::prefix('root')
    ->name('root.')
    ->middleware(['auth', 'role:root'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | DASHBOARD
        |--------------------------------------------------------------------------
        */

        Route::view('/', 'root.dashboard')
            ->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | USERS
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'users',
            UserController::class
        );

        /*
        |--------------------------------------------------------------------------
        | COMPANIES
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'companies',
            CompanyController::class
        );


        /*
        |--------------------------------------------------------------------------
        | INTERNS
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'interns',
            InternController::class
        );


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
            'internship-program-banners',
            InternshipProgramBannerController::class
        );


        /*
        |--------------------------------------------------------------------------
        | INTERNSHIP POSITIONS
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'internship-positions',
            InternshipPositionController::class
        );


        /*
        |--------------------------------------------------------------------------
        | INTERNSHIP REGISTRATIONS
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'internship-registrations',
            InternshipRegistrationController::class
        );


        /*
        |--------------------------------------------------------------------------
        | INTERNSHIP PARTICIPANTS
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'internship-participants',
            InternshipParticipantController::class
        );


        /*
        |--------------------------------------------------------------------------
        | CERTIFICATES
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'certificates',
            CertificateController::class
        );


        /*
        |--------------------------------------------------------------------------
        | WORKS
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'works',
            WorkController::class
        );


        /*
        |--------------------------------------------------------------------------
        | WORK PHOTOS
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'work-photos',
            WorkPhotoController::class
        );


        /*
        |--------------------------------------------------------------------------
        | WORK MEMBERS
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'work-members',
            WorkMemberController::class
        );

    });