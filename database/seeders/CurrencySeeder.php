<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('currencies')->insert([
            ['currency_name' => 'KSH', 'is_active' => '1', 'order' => '1'],
            ['currency_name' => 'UGX', 'is_active' => '1', 'order' => '1'],
            ['currency_name' => 'TZSH', 'is_active' => '1', 'order' => '1'],
            ['currency_name' => 'USD', 'is_active' => '1', 'order' => '2'],
            ['currency_name' => 'EUR', 'is_active' => '1', 'order' => '3'],
            ['currency_name' => 'GBP', 'is_active' => '1', 'order' => '4'],
            ['currency_name' => 'JPY', 'is_active' => '1', 'order' => '5'],
            ['currency_name' => 'CNY', 'is_active' => '1', 'order' => '6'],
            ['currency_name' => 'INR', 'is_active' => '1', 'order' => '7'],
            ['currency_name' => 'AUD', 'is_active' => '1', 'order' => '8'],
            ['currency_name' => 'CAD', 'is_active' => '1', 'order' => '9'],
            ['currency_name' => 'CHF', 'is_active' => '1', 'order' => '10'],
            ['currency_name' => 'SGD', 'is_active' => '1', 'order' => '11'],
            ['currency_name' => 'HKD', 'is_active' => '1', 'order' => '12'],
            ['currency_name' => 'NZD', 'is_active' => '1', 'order' => '13'],
            ['currency_name' => 'ZAR', 'is_active' => '1', 'order' => '14'],
            ['currency_name' => 'BRL', 'is_active' => '1', 'order' => '15'],
            ['currency_name' => 'MXN', 'is_active' => '1', 'order' => '16'],
            ['currency_name' => 'RUB', 'is_active' => '1', 'order' => '17'],
            ['currency_name' => 'KRW', 'is_active' => '1', 'order' => '18'],
            ['currency_name' => 'SEK', 'is_active' => '1', 'order' => '19'],
            ['currency_name' => 'NOK', 'is_active' => '1', 'order' => '20'],
            ['currency_name' => 'DKK', 'is_active' => '1', 'order' => '21'],
            ['currency_name' => 'TRY', 'is_active' => '1', 'order' => '22'],
            ['currency_name' => 'AED', 'is_active' => '1', 'order' => '23'],
            ['currency_name' => 'SAR', 'is_active' => '1', 'order' => '24'],
            ['currency_name' => 'THB', 'is_active' => '1', 'order' => '25'],
            ['currency_name' => 'MYR', 'is_active' => '1', 'order' => '26'],
            ['currency_name' => 'IDR', 'is_active' => '1', 'order' => '27'],
            ['currency_name' => 'PHP', 'is_active' => '1', 'order' => '28'],
            ['currency_name' => 'PLN', 'is_active' => '1', 'order' => '29'],
            ['currency_name' => 'HUF', 'is_active' => '1', 'order' => '30'],
        ]);
    }
}
