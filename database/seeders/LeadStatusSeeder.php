<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lead_statuses')->insert([
            ['lead_status_name' => 'Pending', 'is_active' => '1', 'color_code' => 'info'],
            ['lead_status_name' => 'Paid', 'is_active' => '1', 'color_code' => 'success'],
            ['lead_status_name' => 'Partially Paid', 'is_active' => '1', 'color_code' => 'success'],
            ['lead_status_name' => 'Overdue', 'is_active' => '1', 'color_code' => 'warning'],
            ['lead_status_name' => 'Legal Escalation', 'is_active' => '1', 'color_code' => 'warning'],
            ['lead_status_name' => 'Disputed', 'is_active' => '1', 'color_code' => 'danger'],
        ]);
    }
}
