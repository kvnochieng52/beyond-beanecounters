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
        Schema::create('additional_cost_rules', function (Blueprint $table) {
            $table->id();
            $table->string("title")->nullable();
            $table->string("type")->nullable();
            $table->string("rule_code")->nullable();
            $table->string("cost_type")->nullable();
            $table->decimal("value")->nullable();
            $table->integer("days")->nullable();
            $table->text("description")->nullable();
            $table->integer("is_active")->nullable();
            $table->integer("apply_due_date")->nullable();
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
        Schema::dropIfExists('additional_cost_rules');
    }
};
