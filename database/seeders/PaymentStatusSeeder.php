<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_statuses')->insert([
            ['payment_status_name' => 'PENDING', 'is_active' => '1', 'color_code' => 'info'],
            ['payment_status_name' => 'PAID', 'is_active' => '1', 'color_code' => 'success'],
            ['payment_status_name' => 'FAILED', 'is_active' => '1', 'color_code' => 'danger'],
        ]);
    }
}
