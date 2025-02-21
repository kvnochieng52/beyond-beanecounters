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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->text("task_title")->nullable();
            $table->dateTime("due_date")->nullable();
            $table->text("description")->nullable();
            $table->integer("priority_id")->nullable();
            $table->bigInteger("lead_id")->nullable();
            $table->bigInteger("assigned_team_id")->nullable();
            $table->bigInteger("assigned_user_id")->nullable();
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
        Schema::dropIfExists('tasks');
    }
};
