<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Ramsey\Uuid\Type\Decimal;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("defaulter_type_id")->nullable();
            $table->string("title")->nullable();
            $table->text("id_passport_number")->nullable();
            $table->text("account_number")->nullable();
            $table->bigInteger("gender_id")->nullable();
            $table->string("telephone")->nullable();
            $table->string("alternate_telephone")->nullable();
            $table->string("email")->nullable();
            $table->string("alternate_email")->nullable();
            $table->bigInteger("country_id")->nullable();
            $table->string("town")->nullable();
            $table->text("address")->nullable();
            $table->string("occupation")->nullable();
            $table->string("company_name")->nullable();
            $table->string("description")->nullable();
            $table->string("kin_full_names")->nullable();
            $table->string("kin_telephone")->nullable();
            $table->string("kin_email")->nullable();
            $table->string("kin_relationship")->nullable();
            $table->bigInteger("assigned_agent")->nullable();
            $table->bigInteger("assigned_department")->nullable();
            $table->bigInteger('institution_id')->nullable();
            $table->decimal('amount')->nullable();
            $table->decimal('balance')->nullable();
            // $table->bigInteger("lead_type_id")->nullable();
            $table->bigInteger("currency_id")->nullable();
            $table->bigInteger("status_id")->nullable();
            $table->bigInteger("stage_id")->nullable();
            $table->bigInteger("category_id")->nullable();
            $table->bigInteger("priority_id")->nullable();
            $table->bigInteger("industry_id")->nullable();
            $table->bigInteger("conversion_status_id")->nullable();
            $table->bigInteger("engagement_level_id")->nullable();
            $table->date("due_date")->nullable();
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
        Schema::dropIfExists('leads');
    }
};
