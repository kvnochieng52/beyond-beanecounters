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
        Schema::create('call_disposition_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("call_disposition_id")->nullable();
            $table->bigInteger("lead_id")->nullable();
            $table->text("description")->nullable();
            $table->bigInteger('created_by')->nullable();
            $table->bigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_disposition_histories');
    }
};
