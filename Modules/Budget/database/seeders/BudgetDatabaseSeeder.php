<?php

namespace Modules\Budget\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Budget\Models\AnneeFinanciere;

class BudgetDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            AnneeFinanciereSeeder::class,
        ]);
    }
}
