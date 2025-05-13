<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('genders')->insert([
            ['gender_name' => 'Male', 'is_active' => '1', 'order' => '1'],
            ['gender_name' => 'Female', 'is_active' => '1', 'order' => '1'],
        ]);
    }
}
