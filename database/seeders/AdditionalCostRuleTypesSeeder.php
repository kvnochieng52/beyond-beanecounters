<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdditionalCostRuleTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('additional_cost_rule_types')->insert([
            ['rule_type_name' => 'Penalty', 'is_active' => '1'],
            ['rule_type_name' => 'Legal Fees', 'is_active' => '1'],
            ['rule_type_name' => 'Default Fees', 'is_active' => '1'],
            ['rule_type_name' => 'Collection Fees', 'is_active' => '1'],
            ['rule_type_name' => 'Discount', 'is_active' => '1'],
        ]);
    }
}
