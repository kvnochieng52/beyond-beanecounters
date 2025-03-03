<?php

use App\Http\Controllers\LeadController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BulkController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\TextController;

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
    Route::resource('/text', TextController::class);
    Route::resource('contacts', ContactController::class);

    Route::post('/get-contacts', [ContactController::class, 'getContacts']);


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
    Route::post('/delete-calendar', [App\Http\Controllers\CalendarController::class, 'deleteCalendar'])->name('calendar.delete');



    Route::prefix('bulk')->group(function () {
        Route::get('/upload', [BulkController::class, 'showUploadForm'])->name('bulk-upload-form');
        Route::post('/upload', [BulkController::class, 'upload'])->name('bulk-upload');
    });


    Route::prefix('texts')->group(function () {
        //  Route::get('/upload', [BulkController::class, 'showUploadForm'])->name('bulk-upload-form');
        Route::post('/upload_csv', [TextController::class, 'uploadCsv'])->name('texts-upload-csv');
        Route::post('/preview-sms', [TextController::class, 'previewSms'])->name('preview-sms');
    });



    Route::prefix('queue')->group(function () {
        Route::get('/export', [QueueController::class, 'export'])->name('queue.export');
    });



    // Route::prefix('debt')->group(function () {
    //     Route::post('/store-debt', [App\Http\Controllers\DebtController::class, 'storeDebt'])->name('debt.storeDebt');
    // });
});
