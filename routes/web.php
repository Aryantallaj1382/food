<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDiscountRestaurantController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminReportRestaurantController;
use App\Http\Controllers\Admin\AdminRestaurantIntroductionController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\discount\AdminDiscountCodeController;
use App\Http\Controllers\Admin\food\AdminFoodController;
use App\Http\Controllers\Admin\order\ordersController;
use App\Http\Controllers\Admin\restaurant\AdminRestaurantController;
use App\Http\Controllers\Admin\TelephoneOrderController;
use App\Http\Controllers\Admin\Transaction\AdminTransactionController;
use App\Http\Controllers\Admin\User\AdminUserController;
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
        Route::get('/restaurant/{restaurant_id}/food/create',  'create')->name('create');
        Route::post('/restaurant/{restaurant_id}/food', 'store')->name('store');
        Route::get('/restaurant`/food/{id}/edit',  'edit')->name('edit');
        Route::put('/restaurant/{restaurant_id}/food/{id}',  'update')->name('update');
        Route::delete('/restaurant/food/{id}',  'destroy')->name('destroy');
        // ... سایر روت‌ها
    });
    Route::prefix('users')->name('users.')->controller(\App\Http\Controllers\Admin\User\AdminUserController::class)->group(function () {
        route::get('/','index')->name('index');
        route::delete('/{id}','destroy')->name('delete');
        route::get('/show/{id}','show')->name('show');
        Route::get('/users/create', 'create')->name('create');
        Route::post('/users', 'store')->name('store');
    });



    Route::prefix('restaurant-introductions')->group(function () {
        Route::get('', [AdminRestaurantIntroductionController::class, 'index'])->name('restaurant_introductions.index');
        Route::delete('{id}', [AdminRestaurantIntroductionController::class, 'destroy'])->name('restaurant_introductions.destroy');
    });
    Route::prefix('order')->name('orders.')->controller(\App\Http\Controllers\Admin\order\ordersController::class)->group(function () {
        route::get('/','index')->name('index');
        route::get('/show/{id}','show')->name('show');
        Route::patch('/orders/{order}/status', 'updateStatus');
        route::delete('/{id}','destroy')->name('delete');

    });


    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');

    Route::prefix('sliders')->name('sliders.')->controller(\App\Http\Controllers\Admin\SliderController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('//create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{slider}/edit', 'edit')->name('edit');
        Route::put('/{slider}', 'update')->name('update');
        Route::delete('/{slider}', 'destroy')->name('destroy');
    });

    Route::resource('categories', ADminCategoryController::class)->only(['index', 'create', 'store', 'destroy','edit','update']);
    Route::delete('delete/category/{id}', [ADminCategoryController::class, 'delete'])->name('category.delete');
    Route::get('transaction', [AdminTransactionController::class, 'index'])->name('restaurants.balance');
    Route::get('transaction/{id}', [AdminTransactionController::class, 'show'])->name('restaurants.transaction.show');
    Route::get('/{id}/credit', [AdminTransactionController::class, 'createCredit'])->name('credit.create');
    Route::post('/{id}/credit', [AdminTransactionController::class, 'storeCredit'])->name('credit.store');

    Route::prefix('orders')->name('orders.')->group(function() {
        Route::get('telephone/create', [TelephoneOrderController::class, 'create'])->name('telephone.create');
        Route::post('telephone', [TelephoneOrderController::class, 'store'])->name('telephone.store');
        Route::get('users/check', [TelephoneOrderController::class, 'checkUser'])->name('checkUser');
        Route::get('restaurants/{restaurant}/foods', [TelephoneOrderController::class, 'getRestaurantFoods']);

    });
    Route::get('/report/restaurants/show/{restaurant}', [AdminReportRestaurantController::class, 'reports'])->name('restaurants.reports.show');
    Route::get('/report/restaurants', [AdminReportRestaurantController::class, 'index'])->name('restaurants.reports.index');
    Route::get('/admin/restaurants/{restaurant}/reports/sales',
        [AdminReportRestaurantController::class, 'salesReport'])
        ->name('restaurants.reports.sales');
    Route::get('/admin/restaurants/{id}/payouts', [AdminReportRestaurantController::class, 'payouts'])
        ->name('restaurants.reports.payouts');
    Route::get('/admin/restaurants/{id}/orders-count', [AdminReportRestaurantController::class, 'ordersCount'])
        ->name('restaurants.reports.orders_count');
    Route::post('/admin/restaurants/toggle-all', [AdminReportRestaurantController::class, 'toggleAll'])
        ->name('restaurants.toggleAll');
// routes/web.php یا admin.php
    Route::get('/foods/inactivate', [AdminFoodController::class, 'inactiveFoods'])->name('foods.activate');
    // routes/web.php یا admin.php
    Route::patch('foods/{food}/activate-all-options', [AdminFoodController::class, 'activateAllOptions'])
        ->name('foods.activate-all-options');
    Route::patch('/food-options/{option}/toggle', [AdminFoodController::class, 'toggle'])
        ->name('admin.food-options.toggle');
    // routes/web.php
    Route::patch('/orders/{order}/admin-note', [ordersController::class, 'updateAdminNote'])
        ->name('orders.update-admin-note');

    Route::post('users/{user}/block', [AdminUserController::class, 'block'])->name('users.block');
    Route::post('users/{user}/unblock', [AdminUserController::class, 'unblock'])->name('users.unblock');

    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::delete('/notifications/clear', [AdminNotificationController::class, 'clear'])
        ->name('notifications.clear');

    Route::resource('feedback', \App\Http\Controllers\Admin\AdminFeedbackController::class)->only([
        'index', 'update'
    ]);

    // routes/web.php
        Route::get('request-discounts', [AdminDiscountRestaurantController::class, 'index'])->name('request-discounts.index');
    Route::get('restaurant-discounts', [AdminDiscountRestaurantController::class, 'show'])->name('restaurant-discounts.index');
    Route::get('cat', [ADminCategoryController::class, 'x'])->name('categories.show');
});
