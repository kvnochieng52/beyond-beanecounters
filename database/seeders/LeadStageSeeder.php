<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lead_stages')->insert([
            ['lead_stage_name' => 'New Lead', 'is_active' => '1'],
            ['lead_stage_name' => 'Contacted', 'is_active' => '1'],
            ['lead_stage_name' => 'Follow-up Scheduled', 'is_active' => '1'],
            ['lead_stage_name' => 'Negotiation in Progress', 'is_active' => '1'],
            ['lead_stage_name' => 'Converted (Client Acquired)', 'is_active' => '1'],
            ['lead_stage_name' => 'Lost (Not Interested/Unresponsive)', 'is_active' => '1'],
            ['lead_stage_name' => 'Re-engagement (Dormant Lead Revived)', 'is_active' => '1'],

        ]);
    }
}
