<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\food\AdminFoodController;
use App\Http\Controllers\Admin\restaurant\AdminRestaurantController;
use Illuminate\Support\Facades\Route;

Route::get('/',[AdminDashboardController::class,'index'])->name('admin.dashboard');
Route::get('/mt', function () {
    Artisan::Call('migrate', ['--force' => true]);

    dd(Artisan::output());
});

Route::get('/optimize', function () {
    Artisan::call('optimize');
    dd(Artisan::output());
});
Route::get('/',[AdminDashboardController::class,'index'])->name('admin.dashboard');
Route::prefix('admin')->name('admin.')->group(function () {

    Route::prefix('restaurants')->name('restaurants.')->controller(AdminRestaurantController::class)->group(function () {
        route::get('/','index');
        route::get('order/{id}','restaurant_orders')->name('order');
        route::get('map','map')->name('map');
        route::get('/{id}','show')->name('show');


    });
    Route::prefix('foods')->name('foods.')->controller(AdminFoodController::class)->group(function () {
        route::get('/restaurant/{id}','restaurant')->name('restaurant');
    });








});
