<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('transaction_types')->insert([
            ['transaction_type_title' => 'Penalty', 'is_active' => '1'],
            ['transaction_type_title' => 'Payment', 'is_active' => '1'],
            ['transaction_type_title' => 'Discount', 'is_active' => '1',],
        ]);
    }
}
