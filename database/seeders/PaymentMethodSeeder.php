<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment_methods')->insert([
            ['method_name' => 'CASH', 'is_active' => '1'],
            ['method_name' => 'M-PESA', 'is_active' => '1',],
            ['method_name' => 'AIRTEL MONEY', 'is_active' => '1',],
            ['method_name' => 'DEBIT/CREDIT', 'is_active' => '1',],
        ]);
    }
}
