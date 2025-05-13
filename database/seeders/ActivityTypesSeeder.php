<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('activity_types')->insert([
            ['activity_type_title' => 'Notes', 'is_active' => 1, 'order' => 1, 'icon' => 'file'],
            ['activity_type_title' => 'Log Call', 'is_active' => 1, 'order' => 2, 'icon' => 'phone'],
            ['activity_type_title' => 'SMS', 'is_active' => 1, 'order' => 3, 'icon' => 'sms'],
            ['activity_type_title' => 'Email', 'is_active' => 1, 'order' => 4, 'icon' => 'envelope'],
            ['activity_type_title' => 'Reminder', 'is_active' => 1, 'order' => 5, 'icon' => 'calendar'],
            ['activity_type_title' => 'Meeting', 'is_active' => 1, 'order' => 6, 'icon' => 'users'],

        ]);
    }
}
