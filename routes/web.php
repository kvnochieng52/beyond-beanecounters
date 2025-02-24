<?php

use App\Http\Controllers\LeadController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
    Route::resource('/lead', LeadController::class);

    Route::prefix('leads')->group(
        function () {
            Route::post('/leads-update-status', [App\Http\Controllers\LeadController::class, 'updateStatus'])->name('leads-update-status');
        }
    );



    Route::prefix('activity')->group(
        function () {
            Route::post('/store-activity', [App\Http\Controllers\ActivityController::class, 'storeActivity'])->name('store-activity');
        }
    );


    Route::prefix('payment')->group(
        function () {
            Route::post('/store-payment', [App\Http\Controllers\PaymentController::class, 'storePayment'])->name('store-payment');
        }
    );



    Route::get('/calendars', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendars.index');
    Route::get('/calendars/create', [App\Http\Controllers\CalendarController::class, 'create'])->name('calendars.create');
    Route::post('/store-calendar', [App\Http\Controllers\CalendarController::class, 'storeCalendar'])->name('store-calendar');



    // Route::prefix('debt')->group(function () {
    //     Route::post('/store-debt', [App\Http\Controllers\DebtController::class, 'storeDebt'])->name('debt.storeDebt');
    // });
});
