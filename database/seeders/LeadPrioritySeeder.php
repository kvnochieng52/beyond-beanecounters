<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lead_priorities')->insert([
            ['lead_priority_name' => 'Low ', 'is_active' => '1', 'color_code' => 'primary', 'description' => 'Small Amounts, Low Urgency'],
            ['lead_priority_name' => 'Medium ', 'is_active' => '1', 'color_code' => 'warning', 'description' => 'Moderate Debt, Likely to Pay'],
            ['lead_priority_name' => 'High ', 'is_active' => '1', 'color_code' => 'danger', 'description' => 'Large Amounts / Legal Risks'],
        ]);
    }
}
