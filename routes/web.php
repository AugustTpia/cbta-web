<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::view('hub', 'hub')->name('hub');
});

require __DIR__.'/library.php';

require __DIR__.'/settings.php';
