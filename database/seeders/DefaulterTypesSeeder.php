<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaulterTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('defaulter_types')->insert([
            ['defaulter_type_name' => 'Individual', "is_active" => 1, "order" => 1],
            ['defaulter_type_name' => 'Business', "is_active" => 1, "order" => 1],
            ['defaulter_type_name' => 'Government Agency', "is_active" => 1, "order" => 1],
            ['defaulter_type_name' => 'Non Profit', "is_active" => 1, "order" => 1],
            ['defaulter_type_name' => 'Financial Institutions', "is_active" => 1, "order" => 1],
        ]);
    }
}
