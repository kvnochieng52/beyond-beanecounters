<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstitutionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('Institutions')->insert([
            ['institution_name' => 'EQUITY BANK', 'is_active' => '1'],
            ['institution_name' => 'CO-Operative Bank', 'is_active' => '1'],
            ['institution_name' => 'SOFT TOUCH TECHNOLOGIES', 'is_active' => '1'],

        ]);
    }
}
