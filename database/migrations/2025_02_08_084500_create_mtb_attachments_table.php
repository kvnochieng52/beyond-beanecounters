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
        Schema::create('mtb_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mtb_id');
            $table->string('file_name');
            $table->string('original_name');
            $table->bigInteger('file_size');
            $table->string('file_type');
            $table->string('file_path');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('mtb_id')->references('id')->on('mtbs')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mtb_attachments');
    }
};
