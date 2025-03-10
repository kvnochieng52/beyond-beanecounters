<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('transaction_statuses')->insert([
            ['status_name' => 'PENDING', 'is_active' => '1', 'color_code' => 'warning'],
            ['status_name' => 'PAID', 'is_active' => '1', 'color_code' => 'success'],
            ['status_name' => 'POSTED', 'is_active' => '1', 'color_code' => 'success'],
            ['status_name' => 'CANCELLED', 'is_active' => '1', 'color_code' => 'danger'],
            ['status_name' => 'FAILED', 'is_active' => '1', 'color_code' => 'danger'],
        ]);
    }
}
