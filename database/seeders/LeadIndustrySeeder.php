<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadIndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lead_industries')->insert([
            ['lead_industry_name' => 'Finance & Banking', 'is_active' => '1'],
            ['lead_industry_name' => 'Healthcare', 'is_active' => '1'],
            ['lead_industry_name' => 'Real Estate & Construction', 'is_active' => '1'],
            ['lead_industry_name' => 'Retail & E-commerce', 'is_active' => '1'],
            ['lead_industry_name' => 'Manufacturing & Wholesale', 'is_active' => '1'],
            ['lead_industry_name' => 'Technology & IT Services', 'is_active' => '1'],
            ['lead_industry_name' => 'Legal & Professional Services', 'is_active' => '1'],
            ['lead_industry_name' => 'Government & Public Sector', 'is_active' => '1'],
        ]);
    }
}
