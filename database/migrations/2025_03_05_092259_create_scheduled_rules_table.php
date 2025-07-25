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
        Schema::create('scheduled_rules', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("lead_id");
            $table->bigInteger("rule_id");
            $table->string("title")->nullable();
            $table->string("type")->nullable();
            $table->string("rule_code")->nullable();
            $table->bigInteger("cost_type")->nullable();
            $table->decimal("value")->nullable();
            $table->integer("days")->nullable();
            $table->integer("is_active")->nullable();
            $table->bigInteger("created_by")->nullable();
            $table->bigInteger("updated_by")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_rules');
    }
};
