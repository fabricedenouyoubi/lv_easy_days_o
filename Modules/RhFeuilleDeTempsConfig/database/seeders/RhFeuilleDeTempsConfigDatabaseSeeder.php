<?php

namespace Modules\RhFeuilleDeTempsConfig\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\RhFeuilleDeTempsConfig\Models\CodeDeTravail;
use Modules\RhFeuilleDeTempsConfig\Models\CategorieCodeDeTravail;

class RhFeuilleDeTempsConfigDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            CodeDeTravailSeeder::class,
        ]);
    }
}
