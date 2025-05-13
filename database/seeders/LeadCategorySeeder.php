<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lead_categories')->insert([
            ['lead_category_name' => 'Unpaid Loans', 'is_active' => '1'],
            ['lead_category_name' => 'Overdue Invoices', 'is_active' => '1'],
            ['lead_category_name' => 'Credit Card Debt', 'is_active' => '1'],
            ['lead_category_name' => 'Bank Debt', 'is_active' => '1'],
            ['lead_category_name' => 'Mortgage Arrears', 'is_active' => '1'],
            ['lead_category_name' => 'Business Debt (B2B)', 'is_active' => '1'],
            ['lead_category_name' => 'Government Debt', 'is_active' => '1'],
            ['lead_category_name' => 'Medical Debt', 'is_active' => '1'],
            ['lead_category_name' => 'Legal Disputes (Debt-Related Lawsuits)', 'is_active' => '1'],
        ]);
    }
}
