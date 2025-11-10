<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Add missing PTP and payment columns if they don't exist
            if (!Schema::hasColumn('activities', 'ptp_check')) {
                $table->tinyInteger('ptp_check')->default(0)->after('calendar_add');
            }
            if (!Schema::hasColumn('activities', 'act_ptp_amount')) {
                $table->decimal('act_ptp_amount', 15, 2)->nullable()->after('ptp_check');
            }
            if (!Schema::hasColumn('activities', 'act_ptp_date')) {
                $table->date('act_ptp_date')->nullable()->after('act_ptp_amount');
            }
            if (!Schema::hasColumn('activities', 'act_ptp_retire_date')) {
                $table->date('act_ptp_retire_date')->nullable()->after('act_ptp_date');
            }
            if (!Schema::hasColumn('activities', 'act_payment_amount')) {
                $table->decimal('act_payment_amount', 15, 2)->nullable()->after('act_ptp_retire_date');
            }
            if (!Schema::hasColumn('activities', 'act_payment_transid')) {
                $table->string('act_payment_transid')->nullable()->after('act_payment_amount');
            }
            if (!Schema::hasColumn('activities', 'act_payment_method')) {
                $table->bigInteger('act_payment_method')->nullable()->after('act_payment_transid');
            }
            if (!Schema::hasColumn('activities', 'act_call_disposition_id')) {
                $table->bigInteger('act_call_disposition_id')->nullable()->after('act_payment_method');
            }
            if (!Schema::hasColumn('activities', 'ref_text_id')) {
                $table->bigInteger('ref_text_id')->nullable()->after('act_call_disposition_id');
            }

            // Add indexes for performance
            $table->index(['lead_id', 'activity_type_id', 'created_at'], 'idx_lead_activity_created');
            $table->index(['act_ptp_date', 'lead_id'], 'idx_ptp_date_lead');
            $table->index(['created_by', 'created_at'], 'idx_created_by_date');
        });

        // Add unique constraint to prevent duplicate calendar entries
        Schema::table('calendars', function (Blueprint $table) {
            if (Schema::hasTable('calendars')) {
                $table->index(['lead_id', 'start_date_time', 'created_by'], 'idx_calendar_unique_check');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('idx_lead_activity_created');
            $table->dropIndex('idx_ptp_date_lead');
            $table->dropIndex('idx_created_by_date');
        });

        if (Schema::hasTable('calendars')) {
            Schema::table('calendars', function (Blueprint $table) {
                $table->dropIndex('idx_calendar_unique_check');
            });
        }
    }
};