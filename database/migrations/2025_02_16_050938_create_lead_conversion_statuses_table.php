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
        Schema::create('lead_conversion_statuses', function (Blueprint $table) {
            $table->id();
            $table->string("lead_conversion_name")->nullable();
            $table->integer("is_active")->nullable();
            $table->integer("order")->nullable();
            $table->string("color_code")->nullable();
            $table->text("description")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_conversion_statuses');
    }
};
