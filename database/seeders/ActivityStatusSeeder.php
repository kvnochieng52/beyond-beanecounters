<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('activity_statuses')->insert([
            ['activity_status_name' => 'OPEN', 'is_active' => '1', 'color_code' => 'info'],
            ['activity_status_name' => 'CLOSED', 'is_active' => '1', 'color_code' => 'success'],
            ['activity_status_name' => 'CANCELLED', 'is_active' => '1', 'color_code' => 'danger'],

        ]);
    }
}
