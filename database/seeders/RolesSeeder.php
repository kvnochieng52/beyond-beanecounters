<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'Agent', 'guard_name' => 'web'],
            ['name' => 'Field Recovery Officer', 'guard_name' => 'web'],
            ['name' => 'Supervisor', 'guard_name' => 'web'],
            ['name' => 'Team Leader', 'guard_name' => 'web'],
            ['name' => 'Admin', 'guard_name' => 'web'],
        ]);
    }
}
