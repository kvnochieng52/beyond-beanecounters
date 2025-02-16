<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadConversionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lead_conversion_statuses')->insert([
            ['lead_conversion_name' => 'Hot Lead (Ready to Pay/Negotiate)', 'is_active' => '1', 'color_code' => 'info'],
            ['lead_conversion_name' => 'Warm Lead (Interested but Needs Follow-up)', 'is_active' => '1', 'color_code' => 'success'],
            ['lead_conversion_name' => 'Cold Lead (Not Interested Currently)', 'is_active' => '1', 'color_code' => 'success'],
            ['lead_conversion_name' => 'Dead Lead (No Potential for Conversion)', 'is_active' => '1', 'color_code' => 'warning'],
        ]);
    }
}
