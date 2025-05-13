<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TextStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('text_statuses')->insert([
            ['text_status_name' => 'PENDING', 'is_active' => '1', 'color_code' => 'info'],
            ['text_status_name' => 'QUEUED', 'is_active' => '1', 'color_code' => 'warning'],
            ['text_status_name' => 'SENT', 'is_active' => '1', 'color_code' => 'success'],
            ['text_status_name' => 'FAILED', 'is_active' => '1', 'color_code' => 'danger'],
            ['text_status_name' => 'CANCELLED', 'is_active' => '1', 'color_code' => 'danger'],
        ]);
    }
}
