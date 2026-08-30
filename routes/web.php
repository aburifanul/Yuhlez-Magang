<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

require __DIR__.'/root.php';
require __DIR__.'/company.php';
require __DIR__.'/intern.php';