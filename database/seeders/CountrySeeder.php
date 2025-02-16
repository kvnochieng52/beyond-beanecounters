<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('countries')->insert([
            ['country_name' => 'Kenya', 'is_active' => '1', 'order' => '1'],
            ['country_name' => 'Uganda', 'is_active' => '1', 'order' => '1'],
            ['country_name' => 'Tanzania', 'is_active' => '1', 'order' => '1'],
            ['country_name' => 'United States', 'is_active' => '1', 'order' => '2'],
            ['country_name' => 'United Kingdom', 'is_active' => '1', 'order' => '3'],
            ['country_name' => 'Germany', 'is_active' => '1', 'order' => '4'],
            ['country_name' => 'France', 'is_active' => '1', 'order' => '5'],
            ['country_name' => 'Japan', 'is_active' => '1', 'order' => '6'],
            ['country_name' => 'China', 'is_active' => '1', 'order' => '7'],
            ['country_name' => 'India', 'is_active' => '1', 'order' => '8'],
            ['country_name' => 'Australia', 'is_active' => '1', 'order' => '9'],
            ['country_name' => 'Canada', 'is_active' => '1', 'order' => '10'],
            ['country_name' => 'Switzerland', 'is_active' => '1', 'order' => '11'],
            ['country_name' => 'Singapore', 'is_active' => '1', 'order' => '12'],
            ['country_name' => 'Hong Kong', 'is_active' => '1', 'order' => '13'],
            ['country_name' => 'New Zealand', 'is_active' => '1', 'order' => '14'],
            ['country_name' => 'South Africa', 'is_active' => '1', 'order' => '15'],
            ['country_name' => 'Brazil', 'is_active' => '1', 'order' => '16'],
            ['country_name' => 'Mexico', 'is_active' => '1', 'order' => '17'],
            ['country_name' => 'Russia', 'is_active' => '1', 'order' => '18'],
            ['country_name' => 'South Korea', 'is_active' => '1', 'order' => '19'],
            ['country_name' => 'Sweden', 'is_active' => '1', 'order' => '20'],
            ['country_name' => 'Norway', 'is_active' => '1', 'order' => '21'],
            ['country_name' => 'Denmark', 'is_active' => '1', 'order' => '22'],
            ['country_name' => 'Turkey', 'is_active' => '1', 'order' => '23'],
            ['country_name' => 'United Arab Emirates', 'is_active' => '1', 'order' => '24'],
            ['country_name' => 'Saudi Arabia', 'is_active' => '1', 'order' => '25'],
            ['country_name' => 'Thailand', 'is_active' => '1', 'order' => '26'],
            ['country_name' => 'Malaysia', 'is_active' => '1', 'order' => '27'],
            ['country_name' => 'Indonesia', 'is_active' => '1', 'order' => '28'],
            ['country_name' => 'Philippines', 'is_active' => '1', 'order' => '29'],
            ['country_name' => 'Poland', 'is_active' => '1', 'order' => '30'],
            ['country_name' => 'Hungary', 'is_active' => '1', 'order' => '31'],
        ]);
    }
}
