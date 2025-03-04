<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegionsTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('regions')->insert([
            ['region_name' => 'NAIROBI', 'order' => null, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['region_name' => 'NAKURU', 'order' => null, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['region_name' => 'MOMBASA', 'order' => null, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['region_name' => 'KISUMU', 'order' => null, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
