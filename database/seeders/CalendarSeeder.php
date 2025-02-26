<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('calendars')->insert([
            [
                'calendar_title' => 'Project Kickoff Meeting',
                'start_date_time' => Carbon::now()->addDays(1)->setTime(10, 0),
                'due_date_time' => Carbon::now()->addDays(1)->setTime(11, 0),
                'description' => 'Initial project kickoff meeting with all stakeholders.',
                'priority_id' => 1,
                'lead_id' => 101,
                'assigned_team_id' => 2,
                'assigned_user_id' => 5,
                'status_id' => 1,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'calendar_title' => 'Client Presentation',
                'start_date_time' => Carbon::now()->addDays(3)->setTime(14, 0),
                'due_date_time' => Carbon::now()->addDays(3)->setTime(15, 30),
                'description' => 'Present project updates to the client.',
                'priority_id' => 2,
                'lead_id' => 102,
                'assigned_team_id' => 3,
                'assigned_user_id' => 7,
                'status_id' => 2,
                'created_by' => 1,
                'updated_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'calendar_title' => 'Internal Review',
                'start_date_time' => Carbon::now()->addDays(5)->setTime(9, 0),
                'due_date_time' => Carbon::now()->addDays(5)->setTime(10, 30),
                'description' => 'Internal review of project progress.',
                'priority_id' => 3,
                'lead_id' => 103,
                'assigned_team_id' => 4,
                'assigned_user_id' => 8,
                'status_id' => 3,
                'created_by' => 2,
                'updated_by' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
