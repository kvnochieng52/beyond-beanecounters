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


        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string("institution_name")->nullable();
            $table->text("address")->nullable();
            $table->text("email")->nullable();
            $table->text("website")->nullable();
            $table->text("telephone")->nullable();
            $table->text("contact_person")->nullable();
            $table->integer("is_active")->nullable();
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
        Schema::dropIfExists('institutions');
    }
};
