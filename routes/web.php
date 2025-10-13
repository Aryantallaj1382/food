<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/mt', function () {
    Artisan::Call('migrate', ['--force' => true]);

    dd(Artisan::output());
});

Route::get('/optimize', function () {
    Artisan::call('optimize');
    dd(Artisan::output());
});
