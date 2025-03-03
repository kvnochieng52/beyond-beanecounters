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
        Schema::create('texts', function (Blueprint $table) {
            $table->id();
            $table->string('text_title')->nullable();
            $table->string('contact_type')->nullable();
            $table->text('recepient_contacts')->nullable();
            $table->text('csv_file_path')->nullable();
            $table->text('csv_file_name')->nullable();
            $table->text('csv_file_columns')->nullable();
            $table->text('contact_list')->nullable();
            $table->text('message')->nullable();
            $table->integer('scheduled')->nullable();
            $table->dateTime('schedule_date')->nullable();
            $table->integer('status')->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->bigInteger('contacts_count')->nullable();
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('texts');
    }
};
