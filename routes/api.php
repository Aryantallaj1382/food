<?php

use App\Http\Controllers\Api\Auth\CheckController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\SendOtpController;
use App\Http\Controllers\Api\Food\RestaurantController;
use App\Http\Controllers\Api\Order\FinalOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('auth')->group(function () {
    Route::post('/check', [CheckController::class, 'check']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'register'])->middleware('auth:sanctum');
    Route::post('/sendOtp', [SendOtpController::class, 'sendOtp']);
});
Route::get('/search', [\App\Http\Controllers\Api\Food\SearchController::class,'search']);
Route::get('/main', [\App\Http\Controllers\Api\MainController::class,'index']);
Route::get('/near_restaurant', [\App\Http\Controllers\Api\Profile\nearestRestaurantsController::class,'nearestRestaurants']);

Route::prefix('profile')->middleware('auth:sanctum')->controller(\App\Http\Controllers\Api\Profile\ProfileController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/store', 'store');

});
Route::prefix('profile/address')->middleware('auth:sanctum')->controller(\App\Http\Controllers\Api\Profile\AddressController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/store', 'store');
    Route::post('/update', 'update');
    Route::post('/update_is_main', 'update_is_main');
    Route::delete('/{id}', 'delete');

});
Route::prefix('restaurant')->controller(RestaurantController::class)->group(function () {
   Route::get('/show/{id}', 'show');
   Route::get('/menu/{id}', 'menu');
   Route::get('/times/{id}', 'times');
});
