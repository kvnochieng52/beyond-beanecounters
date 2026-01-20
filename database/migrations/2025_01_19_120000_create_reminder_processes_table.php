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
        Schema::create('reminder_processes', function (Blueprint $table) {
            $table->id();
            $table->string('process_type', 50)->default('ptp_reminder');
            $table->date('process_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('total_customers')->default(0);
            $table->integer('successful_reminders')->default(0);
            $table->integer('failed_reminders')->default(0);
            $table->string('status', 20)->nullable(); // pending, running, completed, failed
            $table->text('error_message')->nullable();
            $table->json('processed_customers')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            $table->index('process_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_processes');
    }
};
