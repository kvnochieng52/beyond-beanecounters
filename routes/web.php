<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\LeadController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BulkController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\TextController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdditionalCostRuleController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DueNotificationController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\TransactionController;

Auth::routes();

Route::group(['middleware' => ['auth', '2fa']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::resource('/lead', LeadController::class);
    Route::resource('/text', TextController::class);
    Route::resource('contacts', ContactController::class);
    Route::post('/get-contacts', [ContactController::class, 'getContacts']);

    Route::resource('additional-cost-rules', AdditionalCostRuleController::class)->names('additional-cost-rules');
    Route::resource('institutions', InstitutionController::class);

    Route::resource('due-notifications', DueNotificationController::class);


    Route::get('institutions-data', [InstitutionController::class, 'getInstitutions'])->name('institutions.getInstitutions');


    Route::get('/transactions/data', [TransactionController::class, 'getTransactions'])->name('transactions.data');

    Route::post('/transactions/store', [TransactionController::class, 'storeTransaction'])->name('transactions.store');


    Route::prefix('leads')->group(
        function () {
            Route::post('/leads-update-status', [LeadController::class, 'updateStatus'])->name('leads-update-status');
        }
    );

    Route::prefix('activity')->group(
        function () {
            Route::post('/store-activity', [ActivityController::class, 'storeActivity'])->name('store-activity');
        }
    );
    Route::prefix('payment')->group(
        function () {
            Route::post('/store-payment', [PaymentController::class, 'storePayment'])->name('store-payment');
        }
    );
    Route::get('/calendars', [CalendarController::class, 'index'])->name('calendars.index');
    Route::get('/calendars/create', [CalendarController::class, 'create'])->name('calendars.create');
    Route::post('/store-calendar', [CalendarController::class, 'storeCalendar'])->name('store-calendar');
    Route::post('/delete-calendar', [CalendarController::class, 'deleteCalendar'])->name('calendar.delete');

    Route::prefix('bulk')->group(function () {
        Route::get('/upload', [BulkController::class, 'showUploadForm'])->name('bulk-upload-form');
        Route::post('/upload', [BulkController::class, 'upload'])->name('bulk-upload');
    });

    Route::prefix('texts')->group(function () {
        Route::post('/upload_csv', [TextController::class, 'uploadCsv'])->name('texts-upload-csv');
        Route::post('/preview-sms', [TextController::class, 'previewSms'])->name('preview-sms');
    });

    Route::prefix('queue')->group(function () {
        Route::get('/export', [QueueController::class, 'export'])->name('queue.export');
    });

    Route::prefix('admin')->group(function () {
        Route::controller(UserController::class)->group(function () {
            Route::get('/roles/create', 'role_create')->name('admin.roles.create');
            Route::get('/roles', 'role_index')->name('admin.roles.index');
            Route::get('/roles/{role_id}/edit', 'role_edit')->name('admin.roles.edit');
            Route::post('/roles/destroy_role', 'destroy_role')->name('admin.roles.destroy');
            Route::post('/roles/role_store', 'role_store')->name('admin.roles.store');
            Route::post('/roles/update_role', 'update_role')->name('admin.roles.update');
        });
        Route::resource('/users', UserController::class)->names('admin.users');
    });
});

Route::prefix('2fa')->group(
    function () {
        Route::get('/', [TwoFactorController::class, 'showVerifyForm'])->name('2fa.verify');
        Route::post('/', [TwoFactorController::class, 'verifyTwoFactorCode']);
        Route::post('/resend', [TwoFactorController::class, 'resend'])->name('2fa.resend');
    }
);
