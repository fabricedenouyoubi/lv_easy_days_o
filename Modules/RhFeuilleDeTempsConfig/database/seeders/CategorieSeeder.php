<?php

namespace Modules\RhFeuilleDeTempsConfig\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\RhFeuilleDeTempsConfig\Models\Categorie;

class CategorieSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'intitule' => 'Heure régulière',
                'configurable' => false,
                'valeur_config' => null,
            ],
            [
                'intitule' => 'Rapport',
                'configurable' => false,
                'valeur_config' => null,
            ],
            [
                'intitule' => 'Absence',
                'configurable' => true,
                'valeur_config' => 'Individuel',
            ],
            [
                'intitule' => 'Caisse',
                'configurable' => true,
                'valeur_config' => 'Individuel',
            ],
            [
                'intitule' => 'Congé',
                'configurable' => true,
                'valeur_config' => 'Individuel',
            ],
            [
                'intitule' => 'Activité',
                'configurable' => true,
                'valeur_config' => 'Collectif',
            ],
            [
                'intitule' => 'Fermé',
                'configurable' => true,
                'valeur_config' => 'Jour',
            ],
        ];

        foreach ($categories as $categoryData) {
            Categorie::create($categoryData);
        }
    }
}
