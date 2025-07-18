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
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransBulkController;

Auth::routes();

Route::group(['middleware' => ['auth']], function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::resource('/lead', LeadController::class);
    Route::resource('/text', TextController::class);
    Route::resource('contacts', ContactController::class);
    Route::post('/get-contacts', [ContactController::class, 'getContacts']);




    Route::resource('additional-cost-rules', AdditionalCostRuleController::class)->names('additional-cost-rules');
    Route::get('scheduled-rules', [AdditionalCostRuleController::class, 'scheduledRules'])->name('scheduled-rules');

    Route::resource('institutions', InstitutionController::class);

    Route::resource('due-notifications', DueNotificationController::class);
    Route::get('institutions-data', [InstitutionController::class, 'getInstitutions'])->name('institutions.getInstitutions');
    Route::get('/transactions/data', [TransactionController::class, 'getTransactions'])->name('transactions.data');
    Route::post('/transactions/store', [TransactionController::class, 'storeTransaction'])->name('transactions.store');
    Route::get('/transactions/{id}/edit', [TransactionController::class, 'editTransaction'])->name('transactions.edit');
    Route::post('/transactions/update', [TransactionController::class, 'updateTransaction'])->name('transactions.update');

    Route::get('/transactions/invoice/{leadID}', [TransactionController::class, 'generateInvoice'])->name('transactions.invoice');

    Route::prefix('leads')->group(
        function () {
            Route::post('/leads-update-status', [LeadController::class, 'updateStatus'])->name('leads-update-status');
            Route::get('/status/{status_id}', [LeadController::class, 'leadByStatus'])->name('lead.leadByStatus');
            Route::get('/leadByStatusData', [LeadController::class, 'leadByStatusData'])->name('lead.leadByStatusData');
            Route::post('/store-ptp', [LeadController::class, 'storePtp'])->name('leads-store-ptp');
            Route::get('/ptps/data', [LeadController::class, 'getPtps'])->name('ptps.data');
            Route::delete('/delete-ptp/{id}', [LeadController::class, 'deletePtp'])->name('leads.delete-ptp');
            Route::get('/export', [LeadController::class, 'export'])->name('lead.export');
            Route::get('/export-by-status/{status}', [LeadController::class, 'exportByStatus'])->name('lead.export-by-status');
            Route::post('/store-call-disposition', [LeadController::class, 'storeCallDisposition'])->name('leads-store-call-disposition');
            Route::get('/call-dispositions/data', [LeadController::class, 'getCallDispositionsData'])->name('call-dispositions.data');
            Route::get('/my-leads/', [LeadController::class, 'myLeads'])->name('my-leads');
        }
    );

    Route::prefix('activity')->group(
        function () {
            Route::get('/', [ActivityController::class, 'allActivity'])->name('all-activity');
            Route::get('/{activity}/edit', [ActivityController::class, 'allEditActivity'])->name('all-edit-activity');
            Route::post('/store-activity', [ActivityController::class, 'storeActivity'])->name('store-activity');
            Route::post('/edit-activity/{activity}', [ActivityController::class, 'editActivity'])->name('edit-activity');
            Route::post('/update-all-activity/{activity}', [ActivityController::class, 'updateAllActivity'])->name('update-activity');
            Route::delete('/destroy/{activity}', [ActivityController::class, 'destroy'])->name('activity.destroy');
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
        Route::post('/text/{id}/edit', [TextController::class, 'edit'])->name('text.edit');
        Route::post('/preview-sms-edit', [TextController::class, 'previewSmsEdit'])->name('preview-sms-edit');
        Route::put('/text/{id}', [TextController::class, 'update'])->name('text.update');
        Route::post('/text/{id}/cancel', [TextController::class, 'cancel'])->name('text.cancel');
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

    Route::prefix('trans-bulk')->group(
        function () {
            Route::get('upload', [TransBulkController::class, 'upload'])->name('trans.bulk.upload');
            Route::post('process', [TransBulkController::class, 'process'])->name('trans.bulk.process');
            Route::get('/', [TransBulkController::class, 'index'])->name('trans_bulk.index');
        }
    );

    // Reports Routes
    Route::prefix('reports')->group(function () {
        // Collection Rates Report
        Route::get('/collection-rates', [ReportController::class, 'collectionRates'])->name('reports.collection-rates');
        Route::match(['get', 'post'], '/collection-rates/generate', [ReportController::class, 'generateCollectionRates'])->name('reports.collection-rates.generate');

        // Outstanding Debts Report
        Route::get('/outstanding-debts', [ReportController::class, 'outstandingDebts'])->name('reports.outstanding-debts');
        Route::match(['get', 'post'], '/outstanding-debts/generate', [ReportController::class, 'generateOutstandingDebts'])->name('reports.outstanding-debts.generate');

        // Agent Performance Report
        Route::get('/agent-performance', [ReportController::class, 'agentPerformance'])->name('reports.agent-performance');
        Route::match(['get', 'post'], '/agent-performance/generate', [ReportController::class, 'generateAgentPerformance'])->name('reports.agent-performance.generate');

        // Collection Progress Report
        Route::get('/collection-progress', [ReportController::class, 'collectionProgress'])->name('reports.collection-progress');
        Route::match(['get', 'post'], '/collection-progress/generate', [ReportController::class, 'generateCollectionProgress'])->name('reports.collection-progress.generate');

        // Agent Leads Report
        Route::get('/agent-leads', [ReportController::class, 'agentLeads'])->name('reports.agent-leads');
        Route::match(['get', 'post'], '/agent-leads/generate', [ReportController::class, 'generateAgentLeads'])->name('reports.agent-leads.generate');
    });
});

Route::prefix('2fa')->group(
    function () {
        Route::get('/', [TwoFactorController::class, 'showVerifyForm'])->name('2fa.verify');
        Route::post('/', [TwoFactorController::class, 'verifyTwoFactorCode']);
        Route::post('/resend', [TwoFactorController::class, 'resend'])->name('2fa.resend');
    }
);
