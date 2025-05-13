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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("lead_id")->nullable();
            $table->integer("transaction_type")->nullable();
            $table->decimal("amount")->nullable();
            $table->text("description")->nullable();
            $table->bigInteger("rule_id")->nullable();
            $table->decimal('balance_before')->nullable();
            $table->decimal('balance_after')->nullable();
            $table->text('transaction_id')->nullable();
            $table->integer("status_id")->nullable();
            $table->integer("penalty_type_id")->nullable();
            $table->integer("payment_method")->nullable();
            $table->integer("transaction_method")->nullable();
            $table->string("charge_type")->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
