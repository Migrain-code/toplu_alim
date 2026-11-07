<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SettingsSeeder::class,
            ServiceSeeder::class,
            // Markalar hizmetlerden SONRA: ana ürün grubuna (category_service_id) bağlanır.
            BrandSeeder::class,
            FeatureSeeder::class,
            RegionSeeder::class,
            FaqSeeder::class,
            PageSeeder::class,
            SeoKeywordSeeder::class,
            // Rehberler kelime havuzundan SONRA: soru kelimeleri yazılara bağlanır.
            BlogSeeder::class,
        ]);
    }
}
