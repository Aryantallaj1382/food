<?php

use App\Http\Controllers\Api\Auth\CheckController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\SendOtpController;
use App\Http\Controllers\Api\Food\RestaurantController;
use App\Http\Controllers\Api\Order\FinalOrderController;
use App\Http\Controllers\Api\Profile\UserOrderController;
use App\Http\Controllers\Api\Restaurant\RestOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('auth')->group(function () {
    Route::post('/check', [CheckController::class, 'check']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/register', [RegisterController::class, 'register'])->middleware('auth:sanctum');
    Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('/sendOtp', [SendOtpController::class, 'sendOtp']);
});
Route::get('/search', [\App\Http\Controllers\Api\Food\SearchController::class,'search']);
Route::get('/main', [\App\Http\Controllers\Api\MainController::class,'index']);
Route::get('/near_restaurant', [\App\Http\Controllers\Api\Profile\nearestRestaurantsController::class,'nearestRestaurants']);

Route::prefix('profile')->middleware('auth:sanctum')->controller(\App\Http\Controllers\Api\Profile\ProfileController::class)->group(function () {
    Route::get('/', 'index');
    Route::post('/store', 'store');
    Route::get('/order', [UserOrderController::class, 'index']);
    Route::get('/order/{id}', [UserOrderController::class, 'show']);
    Route::post('/order/comment/{orderId}', [\App\Http\Controllers\Api\Profile\OrderCommentController::class, 'store']);

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
   Route::get('/time/{id}', 'getAvailableTimes');
   Route::get('/comments/{id}', 'comments');
   Route::post('/toggle/{id}', 'toggle')->middleware('auth:sanctum');
});
Route::prefix('order')->controller(FinalOrderController::class)->group(function () {
    Route::post('/send_price', 'send_price');
    Route::post('/check_discount', 'check_discount')->middleware('auth:sanctum');
    Route::post('/store', 'store')->middleware('auth:sanctum');

});
Route::get('/test', [FinalOrderController::class, 'test']);
//Route::post('/introduction', [\App\Http\Controllers\Api\RestaurantIntroductionController::class, 'store']);



Route::prefix('restaurant')->group(function () {


    Route::prefix('/order')->controller(RestOrderController::class)->group(function () {
        Route::get('/index_order', 'index_order');
        Route::get('/show_order/{id}', 'show_order');

    });














});
