<?php

namespace Modules\RhFeuilleDeTempsConfig\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\RhFeuilleDeTempsConfig\Models\Categorie;
use Modules\RhFeuilleDeTempsConfig\Models\CodeTravail;

class CodeTravailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les catégories créées
        $categories = Categorie::all()->keyBy('intitule');

        $codesTravail = [
            // Codes pour "Heure régulière"
            [
                'code' => 'REG',
                'libelle' => 'Heure régulière',
                'categorie_id' => $categories['Heure régulière']->id,
            ],
            [
                'code' => 'HESUP',
                'libelle' => 'Heure supplémentaire',
                'categorie_id' => $categories['Heure régulière']->id,
            ],
            [
                'code' => 'TDD',
                'libelle' => 'Temps de déplacement',
                'categorie_id' => $categories['Heure régulière']->id,
            ],

            // Codes pour "Rapport"
            [
                'code' => 'FOR',
                'libelle' => 'Formation',
                'categorie_id' => $categories['Rapport']->id,
            ],
            [
                'code' => 'CATDF',
                'libelle' => 'Banque de temps de formation',
                'categorie_id' => $categories['Rapport']->id,
            ],

            // Codes pour "Absence"
            [
                'code' => 'VAC',
                'libelle' => 'Vacances',
                'categorie_id' => $categories['Absence']->id,
            ],

            // Codes pour "Caisse"
            [
                'code' => 'CAISS',
                'libelle' => 'Banque de temps',
                'categorie_id' => $categories['Caisse']->id,
            ],

            // Codes pour "Congé"
            [
                'code' => 'CONMO',
                'libelle' => 'Congés Mobiles',
                'categorie_id' => $categories['Congé']->id,
            ],

            // Codes pour "Activité"
            [
                'code' => 'CSN',
                'libelle' => 'Heure CSN',
                'categorie_id' => $categories['Activité']->id,
            ],

            // Codes pour "Fermé"
            [
                'code' => 'FERIE',
                'libelle' => 'Journée fériée',
                'categorie_id' => $categories['Fermé']->id,
            ],
            [
                'code' => 'HEDIV',
                'libelle' => 'Heures diverses',
                'categorie_id' => $categories['Fermé']->id,
            ],
        ];

        foreach ($codesTravail as $codeData) {
            CodeTravail::create($codeData);
        }
    }
}
