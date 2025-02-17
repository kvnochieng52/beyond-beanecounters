<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            DefaulterTypesSeeder::class,
            CountrySeeder::class,
            CurrencySeeder::class,
            GenderSeeder::class,
            InstitutionSeeder::class,
            LeadCategorySeeder::class,
            LeadConversionStatusSeeder::class,
            LeadEngagementLevelSeeder::class,
            LeadIndustrySeeder::class,
            LeadPrioritySeeder::class,
            LeadStageSeeder::class,
            LeadStatusSeeder::class,
        ]);
    }
}
