<?php

namespace Modules\RhFeuilleDeTempsConfig\Database\Seeders;

use Illuminate\Database\Seeder;

class RhFeuilleDeTempsConfigDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CategorieSeeder::class,
            CodeTravailSeeder::class,
            ConfigurationSeeder::class,
        ]);
    }
}
