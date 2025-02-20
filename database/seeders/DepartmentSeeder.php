<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert([
            ['department_name' => 'Collections Department', 'is_active' => 1, 'order' => 1],
            ['department_name' => 'Field Recovery Department', 'is_active' => 1, 'order' => 2],
            ['department_name' => 'Legal Department', 'is_active' => 1, 'order' => 3],
            ['department_name' => 'Client Relations Department', 'is_active' => 1, 'order' => 4],
            ['department_name' => 'Compliance & Risk Management', 'is_active' => 1, 'order' => 5],
            ['department_name' => 'Data Analysis & Reporting', 'is_active' => 1, 'order' => 6],
            ['department_name' => 'Information Technology (IT)', 'is_active' => 1, 'order' => 7],
            ['department_name' => 'Finance Department', 'is_active' => 1, 'order' => 8],
            ['department_name' => 'Human Resources (HR)', 'is_active' => 1, 'order' => 9],
            ['department_name' => 'Marketing & Business Development', 'is_active' => 10, 'order' => 11],
            ['department_name' => 'Training & Quality Assurance', 'is_active' => 1, 'order' => 12],
        ]);
    }
}
