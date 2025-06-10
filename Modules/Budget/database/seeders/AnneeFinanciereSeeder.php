<?php

namespace Modules\Budget\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Budget\Models\AnneeFinanciere;
use Carbon\Carbon;

class AnneeFinanciereSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Supprimer toutes les années existantes pour éviter les doublons
        AnneeFinanciere::truncate();

        // Créer deux années financières de test
        $anneesFinancieres = [
            [
                'debut' => Carbon::create(2024, 4, 1),
                'fin' => Carbon::create(2025, 3, 31),
                'actif' => false,
            ],
            [
                'debut' => Carbon::create(2025, 4, 1),
                'fin' => Carbon::create(2026, 3, 31),
                'actif' => true,
            ],
        ];

        foreach ($anneesFinancieres as $anneeData) {
            AnneeFinanciere::create($anneeData);
        }

        /* $this->command->info('Années financières créées avec succès :');
        $this->command->info('- 2024-2025 (Active)');
        $this->command->info('- 2023-2024 (Inactive)'); */
    }
}
