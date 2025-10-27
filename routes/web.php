<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\discount\AdminDiscountCodeController;
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
    Route::resource('discount-codes', AdminDiscountCodeController::class);

    Route::prefix('restaurants')->name('restaurants.')->controller(AdminRestaurantController::class)->group(function () {
        route::get('/','index')->name('index');
        route::get('order/{id}','restaurant_orders')->name('order');
        route::get('items/{id}','order_item')->name('items');
        route::get('map','map')->name('map');
        Route::get('/create',  'create')->name('create');
        Route::get('/{restaurant}/edit','edit')->name('edit');
        Route::put('/{restaurant}',  'update')->name('update');
        Route::delete('/{restaurant}',  'destroy')->name('destroy');

        Route::post('/', 'store')->name('store');
        route::get('/{id}','show')->name('show');



    });
    Route::prefix('foods')->name('foods.')->controller(AdminFoodController::class)->group(function () {
        route::get('/restaurant/{id}','restaurant')->name('restaurant');
    });








});
