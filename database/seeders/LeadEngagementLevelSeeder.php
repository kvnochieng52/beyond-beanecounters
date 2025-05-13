<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeadEngagementLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lead_engagement_levels')->insert([
            ['lead_engagement_level_name' => 'Highly Engaged (Actively Communicating)', 'is_active' => '1', 'color_code' => 'success'],
            ['lead_engagement_level_name' => 'Moderately Engaged (Responds Occasionally)', 'is_active' => '1', 'color_code' => 'info'],
            ['lead_engagement_level_name' => 'Unresponsive (No Replies)', 'is_active' => '1', 'color_code' => 'warning'],
            ['lead_engagement_level_name' => 'Disinterested (Explicitly Declined)', 'is_active' => '1', 'color_code' => 'danger'],
        ]);
    }
}
