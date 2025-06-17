<?php

namespace Modules\Entreprise\Database\Seeders;

use Illuminate\Database\Seeder;

class EntrepriseDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            EntrepriseSeeder::class,
            AdresseSeeder::class,
            SiteSeeder::class,
        ]);
    }
}
